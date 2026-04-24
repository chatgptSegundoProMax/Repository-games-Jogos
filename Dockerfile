# Usa uma imagem oficial com PHP e Apache
FROM php:8.2-apache

# Copia todos os ficheiros do teu projeto para a pasta do servidor
COPY . /var/www/html/

# Dá as permissões necessárias
RUN chown -rw /var/www/html/

# Expõe a porta 80
EXPOSE 80
