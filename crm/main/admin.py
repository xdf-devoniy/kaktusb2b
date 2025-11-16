from django.contrib import admin
from django.contrib.auth.admin import UserAdmin as BaseUserAdmin
from .models import User, Dealer, InventoryItem, Design, Order, Room

class RoomInline(admin.TabularInline):
    model = Room
    extra = 1

@admin.register(Order)
class OrderAdmin(admin.ModelAdmin):
    inlines = [RoomInline]
    list_display = ('reference_number', 'dealer', 'deadline', 'status', 'price')
    list_filter = ('status', 'deadline')
    search_fields = ('reference_number', 'dealer__name')

@admin.register(User)
class UserAdmin(BaseUserAdmin):
    fieldsets = BaseUserAdmin.fieldsets + (
        (None, {'fields': ('role',)}),
    )
    add_fieldsets = BaseUserAdmin.add_fieldsets + (
        (None, {'fields': ('role',)}),
    )
    list_display = ('username', 'email', 'first_name', 'last_name', 'is_staff', 'role')
    list_filter = ('role', 'is_staff', 'is_superuser', 'groups')
    search_fields = ('username', 'first_name', 'last_name', 'email')


admin.site.register(Dealer)
admin.site.register(InventoryItem)
admin.site.register(Design)
admin.site.register(Room)
