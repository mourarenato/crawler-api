from flask import Flask, request, jsonify
import scrapy
import subprocess
import json

app = Flask(__name__)

@app.route('/')
def helloWorld():
    return 'Hello, we have Flask in a Docker container'

@app.route('/scrape', methods=['POST'])
def srapeData():
    try:
        data = request.get_json()
        codes = data.get('codes')

        if not codes:
            return jsonify({"error": "You must provide codes argument"}), 400

        command = f"cd isopycrawler/ && scrapy crawl iso4217 -a codes={codes}"
        response = subprocess.run(command, shell=True, capture_output=True, text=True)

        if not json.loads(response.stdout):
            return jsonify({'response': 'Data not found'}), 404

        if response.returncode == 0:
            return jsonify({'response': response.stdout}), 200
        else:
            raise Exception(f'Error while tyring to scrape data: {response.stdout}')

    except Exception as e:
        return jsonify({"error": str(e)}), 500