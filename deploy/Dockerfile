FROM registry-vpc.cn-qingdao.aliyuncs.com/ytlmike/php-fpm-nginx
ENV TZ=Asia/Shanghai
COPY build/ /var/www/leetcoder/
COPY server.conf /etc/nginx/conf.d/default.conf
RUN chown -R www-data /var/www/leetcoder && chgrp -R www-data /var/www/leetcoder

