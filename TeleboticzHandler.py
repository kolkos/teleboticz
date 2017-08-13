"""
File:                 TeleboticzHandler.py

This file contains the methods required to handle the registered Teleboticz
requests. It uses the database to dertermine which requests need to be handled.

class:
    TeleboticzHandler -- resposible for handling the bot requests

    methods:
        __init__
            Initiate this class.



---- Changelog ----

Version:              0.1
Date:                 06-08-2017
Author:               Anton van der Kolk <a.vanderkolk@kolkos.nl>
Changes:              - First draft
"""

import time
from datetime import datetime
from Database import Database
from General import General
from TeleboticzGeneral import TeleboticzGeneral

class TeleboticzHandler(object):
    """
    The TeleboticzHandler class
    """
    def __init__(self):
        self.database = Database()
        self.general = General()
        self.teleboticzgeneral = TeleboticzGeneral()
        return

    def determine_chat_method(self, **kwargs):
        """
        This method determines which method should be called based on the given command
        """
        def func_not_found(**kwargs):
            """
            If the method could not be found
            """
            # log this action
            log_string = 'result="method {} not found"'.format(kwargs['command'])
            self.general.logger(
                1,
                self.__class__.__name__,
                self.determine_chat_method.__name__,
                log_string
            )
            # to send a message to the user, we need a chat_id
            # if this is not found, we can't send the message
            message_to_send = self.general.translate_text('unknown_command')
            if 'chat_id' in kwargs:
                self.teleboticzgeneral.send_chat_message(kwargs['chat_id'], message_to_send)

            return False

        log_string = 'action="Method called", kwargs="{}"'.format(
            str(kwargs)
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.determine_chat_method.__name__,
            log_string
        )
        start_time = time.time()

        # to work properly, this method needs a command
        # so check if the command parameter is given
        if 'command' not in kwargs:
            # command is not found
            log_string = 'error="can\'t find the method parameter"'
            self.general.logger(
                1,
                self.__class__.__name__,
                self.determine_chat_method.__name__,
                log_string
            )
            # exit this method
            return False

        # determine the correct chat method
        method_name = 'handle_command_' + kwargs['command']
        func = getattr(self, method_name, func_not_found)
        result = func(**kwargs)

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}", result="{}"'.format(
            execution_time,
            result
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.determine_chat_method.__name__,
            log_string
        )

        return result

    def handle_chat_messages(self):
        """
        Method to handle the incoming chat messages. These are messages which are handled
        by the TelegramChatHandler class. 
        """
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_chat_messages.__name__,
            'action="Method called"'
        )
        start_time = time.time()
        
        query = "SELECT id, chat_id, message FROM telegram_chat_messages "\
              + "WHERE date_handled = 0 ORDER BY date_in ASC"
        results = self.database.select_handler(query)
        for row in results:
            row_id = row[0]
            chat_id = str(row[1])
            command = row[2]

            log_string = 'action="handling command \'{}\' from \'{}\'"'.format(
                command,
                chat_id
            )
            self.general.logger(
                2,
                self.__class__.__name__,
                self.handle_chat_messages.__name__,
                log_string
            )

            # use the general function to determine what to do with this command
            kwargs = {'chat_id': chat_id, 'command': command}

            self.determine_chat_method(**kwargs)

            # now update the handled chat message
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            query = "UPDATE telegram_chat_messages SET date_handled = ? WHERE id = ?"
            values = [(timestamp, row_id)]
            self.database.update_handler(query, values)

            log_string = 'action="command \'{}\' from \'{}\' handled"'.format(
                command,
                chat_id
            )
            self.general.logger(
                2,
                self.__class__.__name__,
                self.handle_chat_messages.__name__,
                log_string
            )

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.handle_chat_messages.__name__,
            log_string
        )
        return
