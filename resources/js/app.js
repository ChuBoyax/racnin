import { createCart } from './cart';
import { initMenu } from './menu';
import { initMotion } from './motion';
import { initOrder } from './order';
import { initShopStatus } from './shop';

initMotion();

const data = document.getElementById('racnin-data');

if (data) {
    const { shop, items } = JSON.parse(data.textContent);
    const cart = createCart(items);

    initShopStatus(shop);
    initMenu(items);
    initOrder({ cart, items, shop });
}
