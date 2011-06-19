#coding: utf-8

from sqlalchemy import *
from sqlalchemy.orm import relation
from sqlalchemy.ext.declarative import declarative_base

from _config import DB

connect = 'sqlite:///%s' % (DB)
engine = create_engine(connect, encoding='utf8', convert_unicode=True)
metadata = MetaData()
metadata.create_all(engine)
Base = declarative_base()

post_table = Table('posts', metadata,
        Column('id', Integer, primary_key=True),
        Column('title', String),
        Column('url', String),
        Column('link', String),
        Column('content', String),
        Column('formatted_content', String),
        Column('created_time', String),
        Column('modified_time', String),
        Column('is_page', Integer),
        Column('is_isolated', Integer),
        )

sites_setting_table = Table('site_setting', metadata,
        Column('id', Integer, primary_key=True),
        Column('site_name', String),
        Column('site_slogan', String),
        Column('author', String),
        Column('google_analytic_id', String)
        )

metadata.create_all(engine)

class post(Base):
    __tablename__ = 'posts'
    id = Column(Integer, primary_key=True)
    title = Column(String)
    url = Column(String)
    link = Column(String)
    content = Column(String)
    formatted_content = Column(String)
    created_time = Column(String)
    modified_time = Column(String)
    is_isolated = Column(Integer)
    is_page = Column(Integer)

    def db2dict(self):
        values = {
                'id': self.id,
                'title': self.title,
                'url': self.url,
                'link': self.link,
                'content': self.content,
                'formatted_content': self.formatted_content,
                'created_time': self.created_time,
                'modified_time': self.modified_time,
                'is_isolated': self.is_isolated,
                'is_page': self.is_page,
                }
        return values

class site_setting(Base):
    __tablename__ = 'site_setting'
    id = Column(Integer, primary_key=True)
    site_name = Column(String)
    site_slogan = Column(String)
    author = Column(String)
    google_analytic_id = Column(String)
