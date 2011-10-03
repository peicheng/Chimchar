#coding: utf-8

from sqlalchemy import *
from sqlalchemy.ext.declarative import declarative_base

db = 'chimchar.db'
connect = 'sqlite:///%s' % (db)
engine = create_engine(connect, encoding='utf8', convert_unicode=True, echo=False)
Base = declarative_base()

class site_setting(Base):
    __tablename__ = 'site_setting'
    id = Column(Integer, primary_key=True)
    site_name = Column(String)
    author = Column(String)
    site_slogan = Column(String)
    google_analytic_id = Column(String)

class posts(Base):
    __tablename__ = 'posts'
    id = Column(Integer, primary_key=True)
    title = Column(String)
    url = Column(String)
    link = Column(String)
    content = Column(String)
    formatted_content = Column(String)
    is_page = Column(Integer)
    is_isolated = Column(Integer)
    created_time = Column(String)
    modified_time = Column(String)

class minisite(Base):
    __tablename__ = 'minisite'
    id = Column(Integer, primary_key=True)
    title = Column(String)
    url = Column(String)
    content = Column(String)
    formatted_content = Column(String)
    tpl = Column(String)
    style = Column(String)
    created_time = Column(String)
    modified_time = Column(String)

class index(Base):
    __tablename__ = 'index'
    id = Column(Integer, primary_key=True)
    title = Column(String)
    url = Column(String)
    content = Column(String)
    formatted_content = Column(String)
    created_time = Column(String)
    modified_time = Column(String)

Base.metadata.create_all(engine)
