#!/usr/bin/env bash
# Use the `ENTRYPOINT` DeploymentConfig environment variable to specify
# which command to run. This enables the same Dockerfile to be used for
# web and worker processes. This script contains the basics for a standard
# Laravel app but it can be customized to add other entrypoints.

#
sed -i "/server_name YOUR_DOMAIN/c server_name ${SERVER_DOMAIN}}" \
           /etc/nginx/conf.d/default.conf
#
if [ "$ENTRYPOINT" = "complete" ]; then
  echo Starting workers
  php artisan queue:work --tries=1 &
  echo Starting web
  /usr/bin/supervisord -c /supervisord.conf &
  rm -rf storage/tnt
  php artisan index:posts
  php artisan index:tags
  php artisan index:places
  php artisan index:events
  php artisan searchable:all
  php artisan categories:refresh
  php artisan reverse:locations
  php artisan geo:places
  echo Starting schedule_run


  while [ 1 ]
  do
    php artisan schedule:run
    sleep 60
  done

# If the entrypoint is `workers`, run the Laravel worker.
elif [ "$ENTRYPOINT" = "workers" ]; then
  echo Starting workers
  php artisan queue:work --tries=3

# If the entrypoint is `schedule_run`, run the Laravel scheduler.
elif [ "$ENTRYPOINT" = "schedule_run" ]; then
  echo Starting schedule_run
  while [ 1 ]
  do
    php artisan schedule:run
    sleep 60
  done

# If the entrypoint is blank or `web`, run the web supervisord process.
elif [ -z "$ENTRYPOINT" ] || "$ENTRYPOINT" = "web" ]
then
  echo Starting web
  /usr/bin/supervisord -c /supervisord.conf

else
  echo Error, cannot find entrypoint $ENTRYPOINT to start
fi