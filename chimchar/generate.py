#coding: utf-8

from utils import get_settings, get_posts, get_nav, posts, index

post = get_posts()
nav = get_nav(post)
settings = get_settings()

settings['nav'] = nav

# generate posts
posts(settings).rende(post)

# generate index
index(settings).rende(post)
