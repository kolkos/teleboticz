"""
Test database functions
"""

import random
import string
from datetime import datetime
from PythonClasses.Database import Database

db = Database()

user_id_1 = random.randint(1000000,9000000)
query_id_1 = random.randint(10000000,90000000)
user_id_2 = random.randint(1000000,9000000)
query_id_2 = random.randint(10000000,90000000)
data = ''.join(random.choice(string.ascii_uppercase + string.digits) for _ in range(10))

# test insert
query_insert = "INSERT INTO callback_queries (user_id, query_id, data) VALUES (?, ?, ?)"
values_insert = [(user_id_1, query_id_1, data), (user_id_2, query_id_2, data)]
db.update_handler(query_insert, values_insert)

# test update
new_query_id = random.randint(10000000,90000000)
query_update = "UPDATE callback_queries SET query_id = ? WHERE user_id = ?"
values_update = [(new_query_id, user_id_2)]
db.update_handler(query_update, values_update)

# test delete
query_delete = "DELETE FROM callback_queries WHERE query_id = ?"
values_delete = [(query_id_1,)]
db.update_handler(query_delete, values_delete)

# test insert
query_select = "SELECT * FROM callback_queries WHERE user_id = ? AND date_handled = ?"
values_select = (user_id_2, 0)
results = db.select_handler(query_select, values_select)
print str(results)

# now fake a bot handle
for row in results:
    row_id = row[0]
    print row_id
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    query_handle = "UPDATE callback_queries SET date_handled = ? WHERE id = ?"
    values_handle = [(timestamp, row_id)]
    db.update_handler(query_handle, values_handle)
