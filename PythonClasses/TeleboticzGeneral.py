"""
File: TeleboticzGeneral.py

General methods for the Teleboticz Bot
"""

import time
import telepot
from General import General
from Database import Database
from telepot.namedtuple import (
    InlineKeyboardMarkup, InlineKeyboardButton
)

class TeleboticzGeneral(object):
    """
    TeleboticzGeneral class
    """
    def __init__(self):
        self.general = General()
        self.database = Database()
        self.send_bot = telepot.Bot(self.general.config.get("Telegram", "api_key"))

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

    def send_buttons(self, chat_id, message, button_list, overwrite_keyboard_id=None):
        """
        Method to send the generate button list to the
        user. All required fields are set/generated in the
        on_chat_message method
        :param chat_id: the id from the client which send the message
        :param message: the send message
        :param button_list: the button list to send
        """
        log_string = 'chat_id={}, '\
                     'message="{}", '\
                     'button_list="{}"'.format(chat_id,
                                               message,
                                               str(button_list))
        self.general.logger(
            3,
            self.__class__.__name__,
            self.send_buttons.__name__,
            log_string)
        start_time = time.time()
        # by default 1 column is used, if the ammount of lines exceeds
        # 10, then two columns will be used.
        columns = 1
        if len(button_list) > 10:
            columns = 2

        # create the inline keyboard markup
        markup = InlineKeyboardMarkup(inline_keyboard=self.general.build_menu(button_list, columns))

        # if overwrite_keyboard_id is None, then a new keyboard is created
        if overwrite_keyboard_id is None:
            # first create a temporary object to send the inline keyboard
            # this object is used to determine the msg_id
            message_with_inline_keyboard = self.send_bot.sendMessage(
                chat_id,
                message,
                reply_markup=markup
            )
            # now get the msg_id
            msg_idf = telepot.message_identifier(message_with_inline_keyboard)

            # register this keyboard in the database
            query = "INSERT INTO telegram_inline_keyboard_messages "\
                + "(chat_id, msg_id, message) "\
                + "VALUES (?, ?, ?)"
            values = [(chat_id, msg_idf[1], message)]
            self.database.update_handler(query, values)

        execution_time = time.time() - start_time
        log_string = 'action="BMethod finished", '\
                   + 'chat_id="{}", '\
                   + 'msg_id="{}", '\
                   + 'message="{}", '\
                   + 'markup="{}", '\
                   + 'execution_time="{}"'
        log_string = log_string.format(
            chat_id,
            msg_idf,
            message,
            markup,
            execution_time)
        self.general.logger(
            3,
            self.__class__.__name__,
            self.send_chat_message.__name__,
            log_string
        )

    def answer_callback_query(self, query_id, message):
        """
        General method to answer a callback query. This isn't a chat message but
        a message in the form of a onscreen notification
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.answer_callback_query.__name__,
            'action="Method called", query_id="{}", message="{}"'.format(
                query_id,
                message)
        )
        start_time = time.time()

        self.send_bot.answerCallbackQuery(query_id, message)

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.answer_callback_query.__name__,
            log_string
        )
