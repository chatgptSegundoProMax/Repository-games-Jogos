FROM php:8.2-apache

# Copia todos os arquivos do projeto para o diretório padrão do Apache
COPY . /var/www/html/

# Ajusta permissões para o usuário do Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

# Comando padrão do container já é iniciar o Apache no foreground no php:apache
