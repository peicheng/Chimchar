#coding: utf-8

import os
import web
from jinja2 import Environment, FileSystemLoader
import markdown
import hashlib

from db import post, site_setting
import db.reader as reader
import db.writer as writer

from secret import SECRET

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
    def check_auth(self):
        if web.cookies().get('is_pass') != SECRET:
            web.redirect('/auth')
        return

    def __init__(self):
        self.check_auth()
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


class auth_handler:
    def GET(self):
        site_info = reader.get_site_setting()
        if site_info == None:
            site_info = site_setting()
            site_info.site_name = 'demo'
            site_info.site_slogan = ''
            site_info.author = 'chimchar'
            site_info.google_analytic_id = ''
            writer.set_site_setting(site_info)
        site_info.site_domain = web.ctx.host
        template_values = {
                'site_info': site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'writer')
        return render_template('auth.html', path, template_values)

    def POST(self):
        site_info = reader.get_site_setting()
        if site_info == None:
            site_info = site_setting()
            site_info.site_name = 'demo'
            site_info.site_slogan = ''
            site_info.author = 'chimchar'
            site_info.google_analytic_id = ''
            writer.set_site_setting(site_info)
        site_info.site_domain = web.ctx.host
        template_values = {
                'site_info': site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'writer')

        pwd = web.input().passwd
        sha1 = hashlib.sha1(pwd).hexdigest()
        if (sha1 == SECRET):
            web.setcookie('is_pass', sha1, 3600)
            web.redirect('/overview')
        else:
            template_values['message'] = 'Secret pharse WRONG!'
            return render_template('auth.html', path, template_values)

class remove_handler(before_request):
    def GET(self, id):
        post = reader.get_post_by_id(id)
        if post:
            writer.remove_post(post)
        web.redirect('/overview')
    def POST(self, id):
        post = reader.get_post_by_id(id)
        if post:
            writer.remove_post(post)
        web.redirect('/overview')

class overview_handler(before_request):
    def GET(self):
        template_values = {
                'site_info': self.site_info,
                'posts': reader.get_all()
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'writer')
        return render_template('overview.html', path, template_values)

class write_handler(before_request):
    def GET(self, id=''):
        template_values = {
                'site_info': self.site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'writer')

        if (id):
            template_values['mode'] = 'edit'
            template_values['post'] = reader.get_post_by_id(id)
        else:
            template_values['mode'] = 'new'
            template_values['post'] = post()

        return render_template('write.html', path, template_values)

class post_handler(before_request):
    def GET(self, id=''):
        web.redirect('/overview')

    def POST(self, id=''):
        p = web.input()
        p.id = id
        writer.set_post_by_id(p)
        web.redirect('/overview')

class settings_handler(before_request):
    def GET(self):
        template_values = {
                'site_info': self.site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'writer')
        return render_template('settings.html', path, template_values)

    def POST(self):
        s = web.input()
        writer.set_site_setting(s)
        web.redirect('/overview')

urls = (
        '/auth', 'auth_handler',
        '/overview', 'overview_handler',
        '/settings', 'settings_handler',
        '/new', 'write_handler',
        '/save', 'post_handler',
        '/edit/([0-9a-zA-Z\-\_]+)', 'write_handler',
        '/update/([0-9a-zA-Z\-\_]+)', 'post_handler',
        '/remove/([0-9a-zA-Z\-\_]+)', 'remove_handler',
        '', 'overview_handler',
        '/', 'overview_handler',
        )

web.config.debug = False
app = web.application(urls, locals())
