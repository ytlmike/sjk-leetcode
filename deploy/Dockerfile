FROM dr4g0nsr/nginx-php-redis:latest
ENV TZ=Asia/Shanghai
COPY build/ /var/www/leetcoder/
COPY server.conf /etc/nginx/sites-enabled/default.conf
RUN chown -R www-data /var/www/leetcoder && chgrp -R www-data /var/www/leetcoder

