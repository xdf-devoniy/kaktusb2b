from django import forms
from .models import Order

class OrderForm(forms.ModelForm):
    class Meta:
        model = Order
        fields = ['reference_number', 'dealer', 'deadline', 'pattern', 'design', 'color', 'baget', 'garfun', 'koltso', 'pattochka', 'kryuchok', 'sketch', 'pechat']
