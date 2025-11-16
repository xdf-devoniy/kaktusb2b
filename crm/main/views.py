from django.shortcuts import render, redirect, get_object_or_404
from django.forms import inlineformset_factory
from .models import Order, Room
from .forms import OrderForm

def order_list(request):
    orders = Order.objects.all()
    return render(request, 'main/order_list.html', {'orders': orders})

def order_detail(request, pk):
    order = get_object_or_404(Order, pk=pk)
    return render(request, 'main/order_detail.html', {'order': order})

def order_create(request):
    RoomFormSet = inlineformset_factory(Order, Room, fields=('name', 'width', 'length', 'commentary', 'material', 'custom_material'), extra=1)
    if request.method == 'POST':
        form = OrderForm(request.POST, request.FILES)
        if form.is_valid():
            order = form.save(commit=False)
            if request.user.is_authenticated:
                order.faktura_tuzuvchi = request.user
            order.save()
            formset = RoomFormSet(request.POST, instance=order)
            if formset.is_valid():
                formset.save()
                return redirect('order_list')
    else:
        form = OrderForm()
        formset = RoomFormSet(instance=Order())
    return render(request, 'main/order_form.html', {'form': form, 'formset': formset})

def update_order_status(request, pk):
    order = get_object_or_404(Order, pk=pk)
    if request.method == 'POST':
        order.status = Order.Status.DOGOVOR
        order.save()
        return redirect('order_detail', pk=order.pk)
    return redirect('order_detail', pk=order.pk)
