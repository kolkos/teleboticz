"""
File: TeleboticzGeneral.py

General methods for the Teleboticz Bot
"""

import time
import telepot
from General import General
from Database import Database

class TeleboticzGeneral(object):
    """
    TeleboticzGeneral class
    """
    def __init__(self):
        self.general = General()
        self.database = Database()
        self.send_bot = object

    def check_user_blacklist(self, user_id):
        """
        Method to check if the user is blacklisted.
        :params user_id: the id of the user
        :returns: True if user is blocked, False if not
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.check_user_blacklist.__name__,
            'action="Method called", user_id="{}"'.format(user_id)
        )
        start_time = time.time()

        query = "SELECT * FROM telegram_users WHERE user_id = ? AND blocked = 1"
        values = (user_id, )
        results = self.database.select_handler(query, values)
        blocked = False
        if len(results) > 0:
            blocked = True

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.check_user_blacklist.__name__,
            log_string
        )

        return blocked

    def register_user(self, user_id, user_name, first_name, last_name):
        """
        This method checks if the user is registered, if not, it will register
        the user.
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.register_user.__name__,
            'action="Method called", user_id="{}"'.format(user_id)
        )
        start_time = time.time()

        query = "SELECT * FROM telegram_users WHERE user_id = ?"
        values = (user_id,)
        results = self.database.select_handler(query, values)
        # check if the query has returned any results, if so we are done
        # else we need to create the user.
        if len(results) > 0:
            return

        # create the user
        query = "INSERT INTO telegram_users (user_id, user_name, first_name, last_name)"\
              + "VALUES (?, ?, ?, ?)"
        values = [(user_id, user_name, first_name, last_name)]
        self.database.update_handler(query, values)

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.register_user.__name__,
            log_string
        )

    def send_chat_message(self, chat_ids, message):
        """
        Method to send a chat message.
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.send_chat_message.__name__,
            'action="Method called", chat_ids="{}", message="{}"'.format(
                chat_ids,
                message)
        )
        start_time = time.time()

        self.send_bot = telepot.Bot(self.general.config.get("Telegram", "api_key"))

        # split the chat ids
        chat_id_array = chat_ids.split(',')

        for chat_id in chat_id_array:
            # send the message
            self.send_bot.sendMessage(chat_id, message)

            # register the outgoing message in the database
            query = "INSERT INTO telegram_send_messages (chat_id, message) "\
                  + "VALUES (?, ?)"
            values = [(chat_id, message)]
            self.database.update_handler(query, values)

            # finally append to log
            self.general.logger(
                3,
                self.__class__.__name__,
                self.send_chat_message.__name__,
                'result="message send", chat_id="{}", message="{}"'.format(
                    chat_id,
                    message)
            )

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.send_chat_message.__name__,
            log_string
        )