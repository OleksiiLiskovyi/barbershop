</div> <footer class="footer">
        <div class="footer-contacts">
            <p><strong>Телефон:</strong> +38 (063) 340-30-07</p>
            <p><strong>Пошта:</strong> lama_barbershop@gmail.com</p>
        </div>
        
        <div class="footer-copyright">
            <p>© 2026 BarberShop Lama | Всі права захищені</p>
        </div>
    </footer>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const burger = document.getElementById('burgerButton');
    const menu = document.querySelector('.menu');

    burger.addEventListener('click', function() {
        menu.classList.toggle('active');
        // Візуальний зворотний зв'язок (зміна тексту кнопки)
        burger.textContent = menu.classList.contains('active') ? '✕' : '☰';
    });
});
</script>
</body>
</html>