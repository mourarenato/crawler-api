import scrapy
import json
import logging
import warnings

warnings.filterwarnings("ignore", category=scrapy.exceptions.ScrapyDeprecationWarning)
logging.getLogger('scrapy').propagate = False

class Iso4217Spider(scrapy.Spider):
    name = "iso4217"
    start_urls = ["https://pt.wikipedia.org/wiki/ISO_4217"]
    dic = {}
    data = []
    currency_locations = []

    def __init__(self, *args, **kwargs):
        super(Iso4217Spider, self).__init__(*args, **kwargs)
        self.codes = kwargs.get('codes', '').split(',')

    def parse(self, response):
        self.startSpider(response, self.codes)
        self.processRows()
        print(json.dumps(self.dic))

    def startSpider(self, response, codes):
        try:
            target_table = response.css("table.wikitable")[0]
            for row in target_table.css("tr"):
                cell_code = row.css("td:nth-child(1) *::text").get()
                cell_code_number = row.css("td:nth-child(2) *::text").get()
                for code in codes:
                    if code == cell_code or code == cell_code_number:
                        self.getRowData(row)
        except Exception as e:
            print(f"Error while starting spider: {e}")


    def getRowData(self, row):
        try:
            cells = row.css("td:nth-child(-n+4) *::text").getall()
            fifth_column_links = row.css("td:nth-child(5) a")
            fifth_column_items = [link.css("::text").get() for link in fifth_column_links]
            fifth_column_items_cleaned = [item.strip() for item in fifth_column_items if item and item.strip()]
            self.currency_locations.append(fifth_column_items_cleaned)
            cells_cleaned = [cell.replace(',', '') for cell in cells if cell.replace(',', '')]  # Remove espaços em branco e elementos vazios
            self.data.append(cells_cleaned)
        except Exception as e:
            print(f"Error while starting spider: {e}")

    def processRows(self):
        try:
            codes = []
            numbers = []
            decimals = []
            currencies = []
            data = self.data
            currency_locations = self.currency_locations

            for item in data:
                if item:
                    codes.append(item[0])
                    numbers.append(item[1])
                    decimals.append(item[2])
                    currencies.append(item[3])

            if len(currency_locations) > len(codes) and currency_locations[0] == []:
                currency_locations.pop(0)

            for index, code in enumerate(codes):
                key = "row" + str(index)
                self.dic[key] = {
                    "code": codes[index], "number": numbers[index], "decimal": decimals[index],
                    "currency": currencies[index], "currency_locations": currency_locations[index]
                }
        except Exception as e:
            print(f"Error while processing row: {e}")


#sudo apt-get install libpq-dev