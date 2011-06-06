#coding: utf-8

import web
from jinja2 import Environment, FileSystemLoader
import os
import time

from db import post, site_setting
import db.reader as reader
import db.writer as writer

import write

def render_template(template_name, path, content):
    extensions = content.pop('extensions', [])
    globals = content.pop('globals', {})

    jinja_env = Environment (
            loader = FileSystemLoader(path),
            extensions = extensions,
            )
    jinja_env.globals.update(globals)
    return jinja_env.get_template(template_name).render(content)

class before_request:
    def __init__(self):
        site_info = reader.get_site_setting()
        if site_info == None:
            site_info = site_setting()
            site_info.site_name = 'demo'
            site_info.site_slogan = ''
            site_info.author = 'chimchar'
            site_info.google_analytic_id = ''
            writer.set_site_setting(site_info)
        site_info.site_domain = web.ctx.host
        self.site_info = site_info

class feed_handler(before_request):
    def GET(self):
        template_values = {
                'site_info': self.site_info,
                'posts': reader.get_all_posts()
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'shared')
        web.header('Content-Type', 'text/xml; charset=UTF-8')
        return render_template('feed.xml', path, template_values)

class robots_handler(before_request):
    def GET(self):
        template_values = {
                'site_info': self.site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'shared')
        web.header('Content-Type', 'text/plain; charset=UTF-8')
        return render_template('robots.txt', path, template_values)

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
        return render_template('sitemap.xml', path, template_values)


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
            return render_template('post.html', path, template_values)
        else:
            raise web.notfound()

class index_handler(before_request):
    def GET(self):
        template_values = {
                'posts': reader.get_all_posts(),
                'pages': reader.get_all_pages(),
                'site_info': self.site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'post')
        return render_template('index.html', path, template_values)

urls = (
        '/', 'index_handler',
        '/writer', write.app,
        '/feed.xml', 'feed_handler',
        '/robots.txt', 'robots_handler',
        '/sitemap.xml', 'sitemap_handler',
        '/([0-9a-zA-Z\-\.]+)', 'post_handler',
        )

app = web.application(urls, locals())

if __name__ == '__main__':
    app.run()
else:
    web.config.debug = False
    application = app.wsgifunc()
