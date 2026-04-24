# Usa a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Instala extensões necessárias se precisar futuramente
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia os arquivos do seu computador para dentro do container
COPY . /var/www/html/

# Corrige as permissões: define o usuário do apache (www-data) como dono dos arquivos
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expõe a porta 80
EXPOSE 80

# Inicia o servidor Apache
CMD ["apache2-foreground"]
