from django.db import models
from django.contrib.auth.models import AbstractUser
from django.utils.translation import gettext_lazy as _
from django.db.models.signals import post_save, post_delete
from django.dispatch import receiver
from decimal import Decimal

class User(AbstractUser):
    class Role(models.TextChoices):
        SALES = 'SALES', _('Sales')
        PRODUCTION = 'PRODUCTION', _('Production')
        BICHUVCHI = 'BICHUVCHI', _('Bichuvchi')
        GARFUN_USTASI = 'GARFUN_USTASI', _('Garfun Ustasi')
        DIZAYNER_USTASI = 'DIZAYNER_USTASI', _('Dizayner Ustasi')
        STANOQ_USTASI = 'STANOQ_USTASI', _('Stanoq Ustasi')

    role = models.CharField(max_length=50, choices=Role.choices)

class Dealer(models.Model):
    name = models.CharField(max_length=255)
    phone_number = models.CharField(max_length=20)

    def __str__(self):
        return self.name

class InventoryItem(models.Model):
    class ItemType(models.TextChoices):
        MATERIAL = 'MATERIAL', _('Material')
        GARFUN = 'GARFUN', _('Garfun')

    name = models.CharField(max_length=255)
    item_type = models.CharField(max_length=50, choices=ItemType.choices)
    width = models.DecimalField(max_digits=10, decimal_places=2, null=True, blank=True)
    length = models.DecimalField(max_digits=10, decimal_places=2, null=True, blank=True)

    def __str__(self):
        return self.name

class Design(models.Model):
    name = models.CharField(max_length=255)
    image = models.ImageField(upload_to='designs/')

    def __str__(self):
        return self.name

class Order(models.Model):
    class Pattern(models.TextChoices):
        UZOR = 'UZOR', _('Uzor')
        FOTOPECHAT = 'FOTOPECHAT', _('Fotopechat')
        OBOY = 'OBOY', _('Oboy')

    class Color(models.TextChoices):
        KORA = 'KORA', _('Qora')
        JIGARRANG = 'JIGARRANG', _('Jigarrang')
        BILAYN = 'BILAYN', _('Bilayn')
        GOLD = 'GOLD', _('Gold')

    class Status(models.TextChoices):
        MULUOQAT = 'MULUOQAT', _('Muloqat')
        DOGOVOR = 'DOGOVOR', _('Dogovor')
        PRODUCTION = 'PRODUCTION', _('Production')
        COMPLETED = 'COMPLETED', _('Completed')


    reference_number = models.CharField(max_length=255, unique=True)
    completion_date = models.DateTimeField(null=True, blank=True)
    dealer = models.ForeignKey(Dealer, on_delete=models.CASCADE)
    deadline = models.DateTimeField()
    pattern = models.CharField(max_length=50, choices=Pattern.choices)
    design = models.ForeignKey(Design, on_delete=models.SET_NULL, null=True, blank=True)
    color = models.CharField(max_length=50, choices=Color.choices)
    baget = models.BooleanField(default=False)
    garfun = models.BooleanField(default=False)
    koltso = models.BooleanField(default=False)
    pattochka = models.BooleanField(default=False)
    kryuchok = models.BooleanField(default=False)
    sketch = models.ImageField(upload_to='sketches/', null=True, blank=True)
    status = models.CharField(max_length=50, choices=Status.choices, default=Status.MULUOQAT)
    price = models.DecimalField(max_digits=10, decimal_places=2, null=True, blank=True)
    pechat = models.BooleanField(default=False)

    faktura_tuzuvchi = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, related_name='sales_orders')
    bichuvchi = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True, related_name='bichuvchi_orders')
    garfun_ustasi = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True, related_name='garfun_ustasi_orders')
    dizayner_ustasi = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True, related_name='dizayner_ustasi_orders')
    stanoq_ustasi = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True, related_name='stanoq_ustasi_orders')

    def __str__(self):
        return self.reference_number

    def calculate_price(self):
        if not self.pk:
            self.price = Decimal('0.00')
            return

        total_price = Decimal('0.00')
        for room in self.rooms.all():
            kvadrat = room.kvadrat
            if room.width >= Decimal('1.5') and room.width <= Decimal('3.6'):
                total_price += kvadrat * Decimal('15000')
            elif room.width > Decimal('3.6'):
                total_price += kvadrat * Decimal('25000')

        if self.pechat:
            total_price *= 2

        self.price = total_price

    def save(self, *args, **kwargs):
        self.calculate_price()
        super().save(*args, **kwargs)


class Room(models.Model):
    order = models.ForeignKey(Order, related_name='rooms', on_delete=models.CASCADE)
    name = models.CharField(max_length=255)
    width = models.DecimalField(max_digits=10, decimal_places=2)
    length = models.DecimalField(max_digits=10, decimal_places=2)
    commentary = models.TextField(blank=True)
    material = models.ForeignKey(InventoryItem, on_delete=models.SET_NULL, null=True, blank=True)
    custom_material = models.CharField(max_length=255, blank=True)

    @property
    def kvadrat(self):
        return self.width * self.length

    @property
    def perimeter(self):
        return 2 * (self.width + self.length)

    def __str__(self):
        return self.name

@receiver(post_save, sender=Room)
@receiver(post_delete, sender=Room)
def update_order_price_on_room_change(sender, instance, **kwargs):
    instance.order.save()
