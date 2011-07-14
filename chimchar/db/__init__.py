#coding: utf-8

import time
import datetime
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker

from config import DB_NAME, TIME_ZONE
from chimchar import post, site_setting

_session = sessionmaker(autoflush=True)

def get_modified_time(tz):
    #TODO utctime
    return datetime.datetime.utcfromtimestamp(\
            time.time() + tz*3600).strftime('%Y-%m-%d %H:%m:%S')

def connect_db():
    connect = 'sqlite:///%s' % (DB_NAME)
    engine = create_engine(connect, encoding='utf8', convert_unicode=True)
    _session.configure(bind=engine)
    return _session()
