#coding: utf-8

import markdown
from sqlalchemy import desc

from chimchar import post, site_setting
from chimchar.db import connect_db
from chimchar.db import get_modified_time

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
        update.modified_time = get_modified_time(8)
        update.is_page = int(s.is_page)
        update.is_isolated = int(s.is_isolated)
        update.is_index = 0
        session.commit()
    else:
        new = post()
        new.title = s.title
        new.url = s.url
        new.link = s.link
        new.content = s.content
        new.formatted_content = markdown.markdown(s.content)
        new.created_time = get_modified_time(8)
        new.modified_time = get_modified_time(8)
        new.is_isolated = int(s.is_isolated)
        new.is_page = int(s.is_page)
        new.is_index = 0
        session.add(new)
        session.commit()

def set_index(s):
    session = connect_db()
    try:
        update = session.query(post).filter_by(is_index = 1).one()
    except:
        update = post()
        session.add(update)
    update.title = s.title
    update.content = s.content
    update.formatted_content = markdown.markdown(s.content)
    update.modified_time = get_modified_time(8)
    update.created_time = get_modified_time(8)
    update.is_index = 1
    update.is_isolated = 1
    update.url = '/'
    session.commit()
