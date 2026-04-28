FROM node:20-alpine

WORKDIR /var/www

COPY package*.json ./
RUN npm install

COPY . .

RUN npm run build

CMD ["sh", "-c", "tail -f /dev/null"]