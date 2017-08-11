"""
File:                 TelegramInlineQueryHandler.py

This fill creates the TelegramInlineQueryHandler class. This class
will be used to handle the Inline Queries.

Inline Queries are the calls when the user types @yourbot_name

class:
    TelegramInlineQueryHandler -- resposible for handling inline queries

    methods:
        __init__
            Initiate this class.

        on_inline_query
            this method will be called on the inline query event


---- Changelog ----

Version:              0.1
Date:                 04-08-2017
Author:               Anton van der Kolk <a.vanderkolk@kolkos.nl>
Changes:              - First draft
"""

import threading
import telepot.helper
from General import General
from telepot.namedtuple import (
    InlineQueryResultArticle, InputTextMessageContent,
    InlineKeyboardMarkup, InlineKeyboardButton
)

class TelegramInlineQueryHandler(telepot.helper.InlineUserHandler,
                                 telepot.helper.AnswererMixin,
                                 telepot.helper.InterceptCallbackQueryMixin):
    """
    TelegramInlineQueryHandler class
    """
    def __init__(self, *args, **kwargs):
        super(TelegramInlineQueryHandler, self).__init__(*args, **kwargs)
        self.general = General()