FROM node:20-alpine

WORKDIR /var/www

COPY package*.json ./
RUN npm install

COPY . .

CMD ["sh", "-c", "rm -f /var/www/public/hot && npm run build && tail -f /dev/null"]