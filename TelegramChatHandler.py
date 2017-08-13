"""
File:                 TelegramChatHandler.py

This file contains the TelegramChatHandlet class. This class is used to
handle incoming chat messages.

class:
    TelegramChatHandler -- responsible for handling chat messages

    methods:
        __init__
            Initiate this class.
            Calls the telepot ChatHandler base class

        send_buttons
            method to send the buttons to the client

        on_chat_message
            general method de termine which action needs
            to be taken with the incoming message. Handling the
            commands is done by seperate methods

        get_scenes
            method to handle the 'scenes' command

        get_switches
            method to handle the 'switches' command

        get_status
            method to handle the 'status' command

        callback_query_options_check
            method to handle the check before calling the corresponding
            method(s)

        on_callback_query
            this method is called when the telegram user clicks
            on a button. The method calls the two methods below
            depending on the action.

        on_callback_query_first_call
            first callback, this will send the possible options
            for the chosen switch/group

        on_callback_query_second_call
            this will process the chosen command in Domoticz

        on_close
            when the time out is reached, this method will be
            triggered. It will remove the buttons in the telegram
            client.


---- Changelog ----

Version:              0.1
Date:                 30-06-2017
Author:               Anton van der Kolk <a.vanderkolk@kolkos.nl>
Changes:              - First draft
"""

import re
import random
import telepot
import telepot.helper
import time
from telepot.namedtuple import (
    InlineKeyboardMarkup, InlineKeyboardButton
)
import emoji
from Domoticz import Domoticz
from General import General
from TeleboticzGeneral import TeleboticzGeneral

class TelegramChatHandler(telepot.helper.ChatHandler, TeleboticzGeneral):
    """
    TelegramChatHandler class
    """
    def __init__(self, *args, **kwargs):
        super(TelegramChatHandler, self).__init__(*args, **kwargs)
        self.domoticz = Domoticz()
        self.general = General()
        self.message_with_inline_keyboard = None
        return
   
    def on_chat_message(self, msg):
        """
        this method is called when the bot receives a chat message.
        this happens when you click on a suggested command
        :param msg: the incoming chat message
        """
        log_string = 'action="Method called", msg="{}"'.format(str(msg))
        self.general.logger(
            3,
            self.__class__.__name__,
            self.on_chat_message.__name__,
            log_string
        )
        start_time = time.time()

        # get info from the incoming message
        content_type, chat_type, chat_id = telepot.glance(msg)

        # if the message isn't text, quit this method
        if content_type != 'text':
            return

        # register the user if needed
        user_id = msg['from']['id']
        user_name = msg['from']['username']
        first_name = msg['from']['first_name']
        last_name = msg['from']['last_name']

        self.register_user(user_id, user_name, first_name, last_name)

        command = msg['text'].lower()

        # now register the incoming call
        query = "INSERT INTO telegram_chat_messages (chat_id, user_id, content_type, chat_type, message) "\
              + "VALUES (?, ?, ?, ?, ?)"
        values = [(chat_id, user_id, content_type, chat_type, command)]
        self.database.update_handler(query, values)

        execution_time = time.time() - start_time
        log_string = 'action="Method finished", execution_time="{}"'.format(
            execution_time,
        )
        self.general.logger(
            3,
            self.__class__.__name__,
            self.on_chat_message.__name__,
            log_string
        )
        return


