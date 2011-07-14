#coding: utf-8

import web
import os
import json

from chimchar.db import reader
from chimchar.utils import before_request
from chimchar.utils import template_render

import writer

class index_handler(before_request):
    def GET(self):
        template_values = {
                'post': reader.get_index(),
                'pages': reader.get_all_pages(),
                'site_info': self.site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'post')
        return template_render('index.html', path, template_values)

class blog_handler(before_request):
    def GET(self):
        template_values = {
                'posts': reader.get_all_posts(),
                'pages': reader.get_all_pages(),
                'site_info': self.site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'post')
        return template_render('posts.html', path, template_values)

class post_handler(before_request):
    def GET(self, url):
        post = reader.get_post_by_url(url)
        if post:
            template_values = {
                    'post': post,
                    'site_info': self.site_info,
                    'pages': reader.get_all_pages()
                    }
            path = os.path.join(os.path.dirname(__file__), 'tpl', 'post')
            return template_render('post.html', path, template_values)
        else:
            raise web.notfound()

class feed_handler(before_request):
    def GET(self):
        template_values = {
                'site_info': self.site_info,
                'posts': reader.get_all_posts()
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'shared')
        web.header('Content-Type', 'text/xml; charset=UTF-8')
        return template_render('feed.xml', path, template_values)

class robots_handler(before_request):
    def GET(self):
        template_values = {
                'site_info': self.site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'shared')
        web.header('Content-Type', 'text/plain; charset=UTF-8')
        return template_render('robots.txt', path, template_values)

class sitemap_handler(before_request):
    def GET(self):
        template_values = {
                'site_info': self.site_info,
                'feed_url': '/feed.xml',
                'site_updated': time.strftime('%Y-%m-%d %H:%M:%S', time.localtime()),
                'posts': reader.get_last_modified(),
                'post_count': len(reader.get_last_modified())
                }
        web.header('Content-Type', 'text/xml; charset=UTF-8')
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'shared')
        return template_render('sitemap.xml', path, template_values)

class latest_json_handler(before_request):
    def GET(self):
        posts = [i.db2dict() for i in reader.get_latest()]
        pages = [i.db2dict() for i in reader.get_all_pages()]
        template_values = {
                'posts': posts,
                'pages': pages,
                }
        web.header('Content-Type', 'text/javascript; charset=UTF-8')
        return json.dumps(template_values)

class post_json_handler(before_request):
    def GET(self, url):
        pages = [i.db2dict() for i in reader.get_all_pages()]
        post = reader.get_post_by_url(url)
        if post:
            template_values = {
                    'post': post.db2dict(),
                    'pages': pages,
                    }
            web.header('Content-Type', 'text/javascript; charset=UTF-8')
            return json.dumps(template_values)
        else:
            raise web.notfound()

urls = (
        '', 'index_handler',
        '/', 'index_handler',
        '/blog', 'blog_handler',
        '/writer', writer.app,
        '/feed.xml', 'feed_handler',
        '/robots.txt', 'robots_handler',
        '/latest.json', 'latest_json_handler',
        '/sitemap.xml', 'sitemap_handler',
        '/([0-9a-zA-Z\-\.]+).json', 'post_json_handler',
        '/([0-9a-zA-Z\-\.]+)', 'post_handler',
        )

web.config.debug = True
app = web.application(urls, locals())

if __name__ == '__main__':
    app.run()
else:
    web.config.debug = False
    application = app.wsgifunc()
