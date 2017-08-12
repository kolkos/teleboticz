from Telegram import Telegram

telegram = Telegram()

user_id = 123
user_name = "Kolkos"
first_name = "Anton"
last_name = "van der Kolk"

telegram.register_user(user_id, user_name, first_name, last_name)

