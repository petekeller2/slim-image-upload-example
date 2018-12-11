#Content of Dockerfile
#Use ADD or COPY command to get your code inside container image
FROM fauria/lamp
 
#Add all files available at current location
COPY src/ /var/www/html/image-upload/
 
