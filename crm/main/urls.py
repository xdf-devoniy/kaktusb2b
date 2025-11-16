from django.urls import path
from . import views

urlpatterns = [
    path('', views.order_list, name='order_list'),
    path('order/new/', views.order_create, name='order_create'),
    path('order/<int:pk>/', views.order_detail, name='order_detail'),
    path('order/<int:pk>/update_status/', views.update_order_status, name='update_order_status'),
]
