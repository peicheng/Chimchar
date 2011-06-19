#coding: utf-8

from sqlalchemy import desc, create_engine
from  sqlalchemy.orm import sessionmaker

from db import DB
from db import post, site_setting

def connect_db():
    connect = 'sqlite:///%s' % (DB)
    engine = create_engine(connect, encoding='utf8', convert_unicode=True)
    Session = sessionmaker(autoflush=True, bind=engine)
    return Session()

def get_site_setting():
    session = connect_db()
    q = session.query(site_setting)
    if q.count() == 0:
        return None
    return q.one()

def get_post_by_url(url):
    session = connect_db()
    q = session.query(post).filter_by(url = url)
    if q.count() == 0:
        return None
    return q.one()

def get_post_by_id(id):
    session = connect_db()
    q = session.query(post).filter_by(id = id)
    if q.count() == 0:
        return None
    return q.one()

def get_all_posts():
    session = connect_db()
    return session.query(post).order_by(\
            desc(post.created_time)).filter_by(is_page = 0, is_isolated = 0).all()

def get_all_pages():
    session = connect_db()
    return session.query(post).order_by(\
            desc(post.created_time)).filter_by(is_page = 1, is_isolated = 0).all()

def get_all_isolated_pages():
    session = connect_db()
    return session.query(post).order_by(\
            desc(post.created_time)).filter_by(is_isolated = 1).all()

def get_all_no_isolated():
    session = connect_db()
    return session.query(post).order_by(\
            desc(post.created_time)).filter_by(is_isolated = 0).all()

def get_all():
    session = connect_db()
    return session.query(post).order_by(desc(post.created_time)).all()

def get_last_modified():
    session = connect_db()
    return session.query(post).order_by(desc(post.modified_time)).all()

def get_latest():
    session = connect_db()
    return session.query(post).order_by(desc(post.created_time)).all()
