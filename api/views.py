import json
import mimetypes
import os
from django.http import JsonResponse, HttpResponse, HttpResponseNotFound
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.db.models import Sum, Q
from django.conf import settings
from .models import CustomUser, Store, Product, Order, OrderItem


def serve_static(request, path):
    file_path = os.path.join(settings.BASE_DIR, path)
    if os.path.isdir(file_path):
        file_path = os.path.join(file_path, 'index.html')
    if not os.path.exists(file_path):
        return HttpResponseNotFound('Not found')
    content_type, _ = mimetypes.guess_type(file_path)
    with open(file_path, 'rb') as f:
        return HttpResponse(f.read(), content_type=content_type or 'application/octet-stream')


def json_response(data, status=200):
    response = JsonResponse(data, status=status, safe=False)
    response["Access-Control-Allow-Origin"] = "*"
    response["Access-Control-Allow-Methods"] = "GET, POST, PUT, DELETE, OPTIONS"
    response["Access-Control-Allow-Headers"] = "Content-Type"
    return response


@csrf_exempt
def products_view(request, pk=None):
    if request.method == "GET":
        if pk:
            try:
                product = Product.objects.values().get(pk=pk)
                return json_response(product)
            except Product.DoesNotExist:
                return json_response({"error": "Product not found"}, 404)
        else:
            qs = Product.objects.all()
            category = request.GET.get("category")
            color = request.GET.get("color")
            price_range = request.GET.get("price_range")

            if category and category != "all":
                qs = qs.filter(category=category)
            if color and color != "all":
                qs = qs.filter(color=color)
            if price_range and price_range != "all":
                if "-" in price_range:
                    parts = price_range.split("-")
                    if len(parts) == 2:
                        qs = qs.filter(price__gte=float(parts[0]), price__lte=float(parts[1]))
                elif price_range == "1000":
                    qs = qs.filter(price__gte=1000)

            return json_response(list(qs.values()))

    if request.method == "POST":
        data = json.loads(request.body)
        product = Product.objects.create(
            name=data.get("name"),
            description=data.get("description"),
            price=data.get("price"),
            category=data.get("category"),
            color=data.get("color"),
            image=data.get("image"),
            rating=data.get("rating", 0),
        )
        return json_response({"id": product.id})

    return json_response({"error": "Method not allowed"}, 405)


@csrf_exempt
def orders_view(request, pk=None):
    if request.method == "GET":
        if pk:
            try:
                order = Order.objects.values().get(pk=pk)
                items = list(OrderItem.objects.filter(order_id=pk).values())
                order["items"] = items
                return json_response(order)
            except Order.DoesNotExist:
                return json_response({"error": "Order not found"}, 404)
        else:
            orders = list(Order.objects.order_by("-created_at").values())
            return json_response(orders)

    if request.method == "POST":
        data = json.loads(request.body)
        order = Order.objects.create(
            customer_name=data.get("customer_name"),
            customer_email=data.get("customer_email"),
            customer_phone=data.get("customer_phone"),
            customer_address=data.get("customer_address"),
            customer_city=data.get("customer_city"),
            customer_pincode=data.get("customer_pincode"),
            total_amount=data.get("total_amount"),
            status="pending",
        )
        for item in data.get("items", []):
            OrderItem.objects.create(
                order=order,
                product_id=item.get("product_id"),
                quantity=item.get("quantity"),
                price=item.get("price"),
            )
        return json_response({"order_id": order.id, "message": "Order placed successfully"})

    return json_response({"error": "Method not allowed"}, 405)


@csrf_exempt
def auth_login(request):
    if request.method != "POST":
        return json_response({"success": False, "message": "Method Not Allowed"}, 405)
    email = request.POST.get("email") or request.POST.get("username")
    password = request.POST.get("password")
    if not email or not password:
        return json_response({"success": False, "message": "Email and password required"})
    try:
        user = CustomUser.objects.get(email=email, status="active")
    except CustomUser.DoesNotExist:
        return json_response({"success": False, "message": "Invalid email or password"})
    if user.check_password(password):
        login(request, user)
        store_id = None
        store_status = None
        if user.role == "store":
            store = Store.objects.filter(user=user).first()
            if store:
                store_id = store.id
                store_status = store.status
        request.session["store_id"] = store_id
        request.session["store_status"] = store_status
        return json_response({
            "success": True,
            "message": "Login successful",
            "user": {
                "id": user.id,
                "username": user.username,
                "email": user.email,
                "role": user.role,
                "phone": user.phone,
                "address": user.address,
            },
            "redirect": get_dashboard_url(user.role),
        })
    return json_response({"success": False, "message": "Invalid email or password"})


@csrf_exempt
def auth_register_user(request):
    if request.method != "POST":
        return json_response({"success": False, "message": "Method Not Allowed"}, 405)
    username = request.POST.get("username")
    email = request.POST.get("email")
    password = request.POST.get("password")
    phone = request.POST.get("phone", "")
    address = request.POST.get("address", "")
    if not username or not email or not password:
        return json_response({"success": False, "message": "All fields are required"})
    if CustomUser.objects.filter(email=email).exists():
        return json_response({"success": False, "message": "Email already registered"})
    user = CustomUser.objects.create_user(
        username=username,
        email=email,
        password=password,
        role="user",
        phone=phone,
        address=address,
        status="active",
    )
    login(request, user)
    return json_response({
        "success": True,
        "message": "Registration successful",
        "redirect": "index.html",
    })


@csrf_exempt
def auth_register_store(request):
    if request.method != "POST":
        return json_response({"success": False, "message": "Method Not Allowed"}, 405)
    username = request.POST.get("username")
    email = request.POST.get("email")
    password = request.POST.get("password")
    phone = request.POST.get("phone", "")
    store_name = request.POST.get("store_name")
    store_description = request.POST.get("store_description", "")
    business_address = request.POST.get("business_address", "")
    gst_number = request.POST.get("gst_number", "")
    if not username or not email or not password or not store_name:
        return json_response({"success": False, "message": "All required fields must be filled"})
    if CustomUser.objects.filter(email=email).exists():
        return json_response({"success": False, "message": "Email already registered"})
    user = CustomUser.objects.create_user(
        username=username,
        email=email,
        password=password,
        role="store",
        phone=phone,
        status="active",
    )
    store = Store.objects.create(
        user=user,
        store_name=store_name,
        store_description=store_description,
        business_address=business_address,
        gst_number=gst_number,
        status="pending",
    )
    login(request, user)
    request.session["store_id"] = store.id
    request.session["store_status"] = store.status
    return json_response({
        "success": True,
        "message": "Store registration successful! Waiting for admin approval.",
        "redirect": "store-dashboard.html",
    })


@csrf_exempt
def auth_logout(request):
    if request.method != "POST":
        return json_response({"success": False, "message": "Method Not Allowed"}, 405)
    logout(request)
    return json_response({"success": True, "message": "Logged out successfully"})


@csrf_exempt
def auth_check_session(request):
    if request.user.is_authenticated:
        return json_response({
            "success": True,
            "logged_in": True,
            "user": {
                "id": request.user.id,
                "username": request.user.username,
                "email": request.user.email,
                "role": request.user.role,
            },
        })
    return json_response({"success": True, "logged_in": False})


@csrf_exempt
def user_dashboard(request):
    if request.method != "POST":
        return json_response({"success": False, "message": "Method Not Allowed"}, 405)
    if not request.user.is_authenticated:
        return json_response({"success": False, "message": "Please login first", "redirect": "login.html"})
    if request.user.role != "user":
        return json_response({"success": False, "message": "Access denied"})
    orders = list(Order.objects.filter(user=request.user).order_by("-created_at").values()[:10])
    total_orders = Order.objects.filter(user=request.user).count()
    return json_response({
        "success": True,
        "orders": orders,
        "total_orders": total_orders,
    })


@csrf_exempt
def store_dashboard(request):
    if request.method != "POST":
        return json_response({"success": False, "message": "Method Not Allowed"}, 405)
    if not request.user.is_authenticated:
        return json_response({"success": False, "message": "Please login first", "redirect": "login.html"})
    if request.user.role != "store":
        return json_response({"success": False, "message": "Access denied"})
    store = Store.objects.filter(user=request.user).first()
    if not store:
        return json_response({"success": False, "message": "Store not found"})
    products = list(Product.objects.filter(store=store).order_by("-created_at").values())
    orders = list(
        Order.objects.filter(
            orderitem__product__store=store
        ).distinct().order_by("-created_at").values()
    )
    return json_response({
        "success": True,
        "products": products,
        "orders": orders,
        "total_products": len(products),
        "total_orders": len(orders),
        "store_status": store.status,
    })


@csrf_exempt
def admin_dashboard(request):
    if request.method != "POST":
        return json_response({"success": False, "message": "Method Not Allowed"}, 405)
    if not request.user.is_authenticated:
        return json_response({"success": False, "message": "Please login first", "redirect": "login.html"})
    if request.user.role != "admin":
        return json_response({"success": False, "message": "Access denied"})
    users = list(CustomUser.objects.order_by("-date_joined").values())
    stores = list(
        Store.objects.select_related("user").order_by("-created_at").values(
            "id", "store_name", "status", "created_at",
            "user__username", "user__email"
        )
    )
    orders = list(Order.objects.order_by("-created_at").values()[:50])
    stats = {
        "total_users": CustomUser.objects.filter(role="user").count(),
        "total_stores": Store.objects.filter(status="approved").count(),
        "pending_stores": Store.objects.filter(status="pending").count(),
        "total_orders": Order.objects.count(),
        "total_revenue": Order.objects.aggregate(total=Sum("total_amount"))["total"] or 0,
    }
    return json_response({
        "success": True,
        "users": users,
        "stores": stores,
        "orders": orders,
        "stats": stats,
    })


@csrf_exempt
def update_store_status(request):
    if request.method != "POST":
        return json_response({"success": False, "message": "Method Not Allowed"}, 405)
    if not request.user.is_authenticated or request.user.role != "admin":
        return json_response({"success": False, "message": "Access denied"})
    store_id = request.POST.get("store_id")
    status = request.POST.get("status")
    if status not in ["approved", "rejected"]:
        return json_response({"success": False, "message": "Invalid status"})
    try:
        store = Store.objects.get(pk=store_id)
        store.status = status
        store.save()
        return json_response({"success": True, "message": f"Store status updated to {status}"})
    except Store.DoesNotExist:
        return json_response({"success": False, "message": "Store not found"})


def test_db(request):
    if request.method != "GET":
        return json_response({"success": False, "message": "Method Not Allowed"}, 405)
    try:
        Product.objects.count()
        return json_response({"success": True, "ready": True})
    except Exception as e:
        return json_response({"success": False, "message": str(e)})


def get_dashboard_url(role):
    if role == "admin":
        return "admin-dashboard.html"
    if role == "store":
        return "store-dashboard.html"
    if role == "user":
        return "user-dashboard.html"
    return "index.html"
