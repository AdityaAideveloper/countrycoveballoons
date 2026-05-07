"""
URL configuration for balloon_planet project.
"""
from django.contrib import admin
from django.urls import path, include, re_path
from api.views import serve_static

urlpatterns = [
    path('admin/', admin.site.urls),
    path('api/', include('api.urls')),
    re_path(r'^(?P<path>.*)$', serve_static),
]
