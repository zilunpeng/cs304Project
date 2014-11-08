drop database if exists ams;

create database ams;

use ams;

drop table if exists item;

create table item
	(upc char(37) not null,
	title varchar(40) not null,
	type varchar(20) not null,
	category varchar(20) not null,
	company varchar(20) not null,
	year integer not null,
	price float not null,
	stock integer not null);
 
drop table if exists leadsinger;

create table leadsinger
	(upc char(37) not null,
	sname varchar(40) not null,
	PRIMARY KEY (upc, sname));
 
 
drop table if exists customer;
 
create table customer
	(cid varchar(40) not null,
	name varchar(40) not null,
	password varchar(40) not null,
	phone varchar(12),
	address varchar(40));
 
 
drop table if exists purchase;
 
create table purchase
	(receiptId int(10) PRIMARY KEY AUTO_INCREMENT,
	purchaseDate date not null,
	cid varchar(40) not null,
	cardNumber int(16) not null,
	expiryDate date not null,
	expectedDate date not null,
	deliveredDate date);
 
 
drop table if exists purchaseItem;
 
create table purchaseItem
	(receiptId int(10),
	upc char(37) not null,
	quantity int not null,
	PRIMARY KEY(receiptId, upc));

 
drop table if exists returns;
 
create table returns
	(retID int(10),
	returnDate date not null,
	receiptId int(10) not null,
	PRIMARY KEY(retID));

 
drop table if exists returnItem;
 
create table returnItem
	(retID int(10),
	upc char(37) not null,
	quantity int not null,
	PRIMARY KEY(retID, upc));


 
insert into item 
values('4a9d51b3-8c71-4546-9974-70029adcfc6a', 'Criminal Of Wood', 'Science fiction', 'country', 'Pearson', 1996, 20.5, 10
 );
 
insert into item 
values('24f7eb56-b006-4a0e-b4e6-5c0091f368dc', 'Prince In The Mountains', 'Novel', 'classical', 'Reed Elsevier', 2005, 19.99, 5
 );
 
insert into item 
values('4d8be8fb-4ccf-4ac9-a037-e89d5e006e32', 'Spiders With Sins', 'Fiction', 'new age', 'Wolters Kluwer', 2011, 10.5, 8
 );
 
insert into item 
values('e2f8561c-d2d0-440d-b662-8ca5cf162274', 'Foreigners Of Hope', 'Poem', 'rock', 'Random House	', 2013, 19, 7
 );
 
insert into item 
values('1d2fc2cc-7ecf-40a8-b404-9b062582a332', 'Rise Of Dawn', 'Drama', 'rock', 'Reed Elsevier	', 2005, 30, 6
 );

insert into leadsinger 
values('4a9d51b3-8c71-4546-9974-70029adcfc6a', 'singer1'
 );
 
insert into leadsinger 
values('24f7eb56-b006-4a0e-b4e6-5c0091f368dc', 'singer1'
 );
 
insert into leadsinger 
values('4d8be8fb-4ccf-4ac9-a037-e89d5e006e32', 'singer2'
 );
 
insert into leadsinger 
values('e2f8561c-d2d0-440d-b662-8ca5cf162274', 'singer2'
 );
 
insert into leadsinger 
values('1d2fc2cc-7ecf-40a8-b404-9b062582a332', 'singer3'
 );

insert into customer
values('001', 'Bennet Abraham', '12345', 
'415-658-9932', '6223 Bateman St Berkeley CA');
 
insert into customer
values ('002', 'Green Marjorie', '12345', 
'415-986-7020', '309 63rd St. #411 Oakland CA');
 
insert into customer
values('003', 'Carson Cheryl', '12345',
'415-548-7723', '589 Darwin Ln. Berkeley CA');
 
insert into customer
values('004', 'Ringer Albert', '12345',
'801-826-0752', '67 Seventh Av. Salt Lake City UT');
 
insert into customer
values('005', 'Ringer Anne', '12345',
'801-826-0752', '67 Seventh Av. Salt Lake City UT');
 
insert into customer
values('006', 'DeFrance Michel', '12345',
'219-547-9982', '3 Balding Pl. Gary IN');
 
insert into customer
values('007', 'Panteley Sylvia', '12345', 
'301-946-8853', '1956 Arlington Pl. Rockville MD');
 
insert into customer
values('008', 'McBadden Heather', '12345',
'707-448-4982', '301 Putnam Vacaville CA');
 
insert into customer
values('009', 'Stringer Dirk', '12345', 
'415-843-2991', '5420 Telegraph Av. Oakland CA');

insert into customer
values('010', 'Tony Allen', '12345', 
'425-843-2891', '3672 Alto Av. Oakland CA');

commit;
