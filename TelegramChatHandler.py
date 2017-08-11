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
from telepot.namedtuple import (
    InlineKeyboardMarkup, InlineKeyboardButton
)
import emoji
from General import General
from Domoticz import Domoticz

class TelegramChatHandler(telepot.helper.ChatHandler):
    """
    TelegramChatHandler class
    """
    def __init__(self, *args, **kwargs):
        super(TelegramChatHandler, self).__init__(*args, **kwargs)
        self.general = General()
        self.domoticz = Domoticz()
        self.message_with_inline_keyboard = None
        return
