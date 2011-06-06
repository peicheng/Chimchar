#coding: utf-8

from sqlalchemy import desc, create_engine
from sqlalchemy.orm import sessionmaker
import markdown
import time

from db import DB
from db import post, site_setting

def connect_db():
    connect = 'sqlite:///%s' % (DB)
    engine = create_engine(connect, encoding='utf8', convert_unicode=True)
    Session = sessionmaker(autoflush=True, bind=engine)
    return Session()

def set_site_setting(s):
    session = connect_db()
    q = session.query(site_setting)

    if q.count() == 0:
        new = site_setting()
        new.site_name = s.site_name
        new.site_slogan = s.site_slogan
        new.author = s.author
        new.google_analytic_id = s.google_analytic_id
        session.add(new)
        session.commit()
    else:
        update = q.one()
        update.site_name = s.site_name
        update.site_slogan = s.site_slogan
        update.author = s.author
        update.google_analytic_id = s.google_analytic_id
        session.commit()
    return

def remove_post(s):
    session = connect_db()
    u = session.query(post).filter_by(id = s.id).one()
    session.delete(u)
    session.commit()

def set_post_by_id(s):
    session = connect_db()
    if s.id:
        update = session.query(post).filter_by(id = s.id).one()
        update.title = s.title
        update.url = s.url
        update.link = s.link
        update.content = s.content
        update.formatted_content = markdown.markdown(s.content)
        update.modified_time = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())
        update.is_page = int(s.is_page)
        update.is_isolated = int(s.is_isolated)
        session.commit()
    else:
        new = post()
        new.title = s.title
        new.url = s.url
        new.link = s.link
        new.content = s.content
        new.formatted_content = markdown.markdown(s.content)
        new.created_time = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())
        new.modified_time = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())
        new.is_isolated = int(s.is_isolated)
        new.is_page = int(s.is_page)
        session.add(new)
        session.commit()
