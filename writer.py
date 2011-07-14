#coding: utf-8

import os
import web
import markdown
import hashlib

from chimchar import post, site_setting
from chimchar.db import reader
from chimchar.db import writer
from chimchar.utils import do_auth
from chimchar.utils import template_render

from config import SECRET

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
        return template_render('auth.html', path, template_values)

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
            return template_render('auth.html', path, template_values)

class remove_handler(do_auth):
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

class overview_handler(do_auth):
    def GET(self):
        template_values = {
                'site_info': self.site_info,
                'posts': reader.get_all()
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'writer')
        return template_render('overview.html', path, template_values)

class write_handler(do_auth):
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

        return template_render('write.html', path, template_values)

class index_write_handler(do_auth):
    def GET(self):
        template_values = {
                'site_info': self.site_info,
                'mode': 'edit',
                'post': reader.get_index()
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'writer')
        return template_render('index_write.html', path, template_values)

class index_update_handler(do_auth):
    def GET(self):
        web.redirect('/overview')

    def POST(self):
        p = web.input()
        writer.set_index(p)
        web.redirect('/overview')

class post_handler(do_auth):
    def GET(self, id=''):
        web.redirect('/overview')

    def POST(self, id=''):
        p = web.input()
        p.id = id
        writer.set_post_by_id(p)
        web.redirect('/overview')

class settings_handler(do_auth):
    def GET(self):
        template_values = {
                'site_info': self.site_info
                }
        path = os.path.join(os.path.dirname(__file__), 'tpl', 'writer')
        return template_render('settings.html', path, template_values)

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
        '/edit/index', 'index_write_handler',
        '/update/index', 'index_update_handler',
        '/edit/([0-9a-zA-Z\-\_]+)', 'write_handler',
        '/update/([0-9a-zA-Z\-\_]+)', 'post_handler',
        '/remove/([0-9a-zA-Z\-\_]+)', 'remove_handler',
        '', 'overview_handler',
        '/', 'overview_handler',
        )

web.config.debug = False
app = web.application(urls, locals())
