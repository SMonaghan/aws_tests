ARG CACHE_BUST_DAY
FROM public.ecr.aws/amazonlinux/amazonlinux:latest
ENV HOME='/root'
RUN yum install amazon-cloudwatch-agent -y
RUN yum install -y aws-cfn-bootstrap
RUN yum install httpd mariadb jq php php-mysql git -y
RUN /usr/bin/amazon-linux-extras install php7.2
RUN yum install php-xml* awscli -y
ARG CACHE_BUST_DAY
RUN yum update -y
ARG CACHE_BUST
RUN git clone https://github.com/SMonaghan/aws_tests.git ~/code
RUN /root/code/composer_install.sh
RUN mv composer.phar /usr/local/bin/composer
RUN /usr/local/bin/composer require aws/aws-sdk-php -d /var/www/html/
RUN /usr/bin/cp ~/code/amazon-cloudwatch-agent.json /opt/aws/amazon-cloudwatch-agent/etc/amazon-cloudwatch-agent.json
RUN /usr/bin/cp ~/code/favicon/* /var/www/html/
RUN /usr/bin/cp ~/code/form_fargate.html /var/www/html/index.html
RUN /usr/bin/cp ~/code/form_submit.php /var/www/html/form_submit.php
RUN /usr/bin/cp ~/code/form_get.php /var/www/html/form_get.php
RUN /bin/echo '<?php' >                                           /var/www/html/dynamic.php
RUN /bin/echo '$host = "lmq84ifawycq6s.cvg7nmcwwoka.us-east-1.rds.amazonaws.com";' >>        /var/www/html/dynamic.php
RUN /bin/echo '$db_name = "LAMPDB";' >>        /var/www/html/dynamic.php
RUN /bin/echo '$username = "admin";' >>         /var/www/html/dynamic.php
RUN /bin/echo '$secretName = "RDSSecret";' >> /var/www/html/dynamic.php
RUN /bin/echo '?>' >>                            /var/www/html/dynamic.php
RUN touch /var/www/html/log.log
RUN chown apache:root /var/www/html/log.log
# CMD [ "/usr/sbin/httpd","-D","FOREGROUND" ]
# RUN systemctl start httpd
# RUN systemctl enable httpd
# RUN curl -O https://inspector-agent.amazonaws.com/linux/latest/install
# RUN bash install
# RUN systemctl start amazon-cloudwatch-agent.service
# RUN systemctl enable amazon-cloudwatch-agent.service
