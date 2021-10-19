Nette

Product eshop

Funkece add produkt, edit produkt, delete product.


List prodact. 


PRidávaní produktu by mělo ject na ajax byl to tesit jak to asi pracuje chtělo to hodně googlení a pataní.

Struktura tabulky `products`

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descriptions` text NOT NULL,
  `amount` int(11) NOT NULL,
  `price` float NOT NULL,
  `photo` varchar(100) NOT NULL,
  `alt` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

