from django.urls import path
from . import views

urlpatterns = [
    path('products/', views.products_view, name='products_list'),
    path('products/<int:pk>/', views.products_view, name='product_detail'),
    path('orders/', views.orders_view, name='orders_list'),
    path('orders/<int:pk>/', views.orders_view, name='order_detail'),
    path('auth/login/', views.auth_login, name='auth_login'),
    path('auth/register/user/', views.auth_register_user, name='auth_register_user'),
    path('auth/register/store/', views.auth_register_store, name='auth_register_store'),
    path('auth/logout/', views.auth_logout, name='auth_logout'),
    path('auth/session/', views.auth_check_session, name='auth_check_session'),
    path('dashboard/user/', views.user_dashboard, name='user_dashboard'),
    path('dashboard/store/', views.store_dashboard, name='store_dashboard'),
    path('dashboard/admin/', views.admin_dashboard, name='admin_dashboard'),
    path('store/update-status/', views.update_store_status, name='update_store_status'),
    path('test/', views.test_db, name='test_db'),
]
