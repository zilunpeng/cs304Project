drop database if exists ams;

create database ams;

use ams;


drop table if exists item;
create table item
	(upc char(3) not null,
	title varchar(40),
	type varchar(20),
	category varchar(20),
	company varchar(20),
	year integer,
	price float,
	stock integer,
    primary key (upc),
    check (type = 'rock' or type = 'pop' or type = 'rap' or type = 'country' or type = 'country' or type = 'classical' or type = 'new age' or type = 'instrumental'));

 
drop table if exists leadSinger;
create table leadSinger
	(upc char(3) not null,
	sname varchar(40) not null,
	primary key (upc, sname));


drop table if exists hasSong;
create table hasSong 
	(upc char(3) not null,
	 title varchar(40) not null,
     primary key (upc, title));
   
   
drop table if exists purchase; 
create table purchase
	(receiptId int(10) not null auto_increment,
	purchaseDate date,
	cid varchar(40),
	cardNumber int(16),
	expiryDate date,
	expectedDate date,
	deliveredDate date,
	primary key (receiptId));
 
 
drop table if exists purchaseItem;
create table purchaseItem 
	(receiptId int(10)  not null,
	upc char(37) not null,
	quantity int,
	primary key(receiptId, upc));
 
 
drop table if exists customer;
create table customer
	(cid varchar(3) not null,
	name varchar(40),
	password varchar(40),
	phone varchar(12),
	address varchar(40),
    primary key(cid));
 

 
drop table if exists returns;
create table returns
	(retID int(10) not null auto_increment,
	returnDate date,
	receiptId int(10),
	primary key(retID));

 
drop table if exists returnItem; 
create table returnItem
	(retID int(10) not null,
	upc char(37) not null,
	quantity int,
	primary key(retID, upc));
 
insert into item 
values('001', 'cd1', 'cd', 'rock', 'Pearson', 1996, 20.5, 10
 );
 
insert into item 
values('002', 'cd2', 'cd', 'pop', 'Reed Elsevier', 2005, 19.99, 5
 );
 
insert into item 
values('003', 'cd3', 'cd', 'rap', 'Wolters Kluwer', 2011, 10.5, 8
 );
 
insert into item 
values('004', 'cd4', 'cd', 'country', 'Wolters Kluwer', 2011, 10.5, 8
 );
 
insert into item 
values('005', 'cd5', 'cd', 'classical', 'Random House	', 2013, 19, 7
 );
 
insert into item 
values('006', 'cd6', 'cd', 'new age', 'Reed Elsevier	', 2005, 30, 6
 );
   
insert into item 
values('007', 'cd7', 'cd', 'instrumental', 'Reed Elsevier	', 2005, 30, 6
 );

insert into item 
values('008', 'cd8', 'cd', 'rock', 'Pearson', 1996, 20.5, 10
 );
 
insert into item 
values('009', 'cd9', 'cd', 'pop', 'Reed Elsevier', 2005, 19.99, 5
 );
 
insert into item 
values('010', 'cd10', 'cd', 'rap', 'Wolters Kluwer', 2011, 10.5, 8
 );
 
insert into item 
values('011', 'cd11', 'cd', 'country', 'Wolters Kluwer', 2011, 10.5, 8
 );
 
insert into item 
values('012', 'cd12', 'cd', 'classical', 'Random House	', 2013, 19, 7
 );
 
insert into item 
values('013', 'cd13', 'cd', 'new age', 'Reed Elsevier	', 2005, 30, 6
 );
   
insert into item 
values('014', 'cd14', 'cd', 'instrumental', 'Reed Elsevier	', 2005, 30, 6
 );
 
 insert into item 
values('015', 'dvd1', 'dvd', 'rock', 'Pearson', 1996, 20.5, 10
 );
 
insert into item 
values('016', 'dvd2', 'dvd', 'pop', 'Reed Elsevier', 2005, 19.99, 5
 );
 
insert into item 
values('017', 'dvd3', 'dvd', 'rap', 'Wolters Kluwer', 2011, 10.5, 8
 );
 
insert into item 
values('018', 'dvd4', 'dvd', 'country', 'Random House	', 2013, 19, 7
 );
 
insert into item 
values('019', 'dvd5', 'dvd', 'classical', 'Reed Elsevier	', 2005, 30, 6
 );
 
insert into item 
values('020', 'dvd6', 'dvd', 'new age', 'Reed Elsevier	', 2005, 30, 6
 );
 
 insert into item 
values('021', 'dvd7', 'dvd', 'instrumental', 'Reed Elsevier', 2005, 19.99, 5
 );
 
insert into item 
values('022', 'dvd8', 'dvd', 'rock', 'Wolters Kluwer', 2011, 10.5, 8
 );
 
insert into item 
values('023', 'dvd9', 'dvd', 'pop', 'Random House	', 2013, 19, 7
 );
 
insert into item 
values('024', 'dvd10', 'dvd', 'rap', 'Reed Elsevier	', 2005, 30, 6
 );
 
insert into item 
values('025', 'dvd11', 'dvd', 'country', 'Reed Elsevier	', 2005, 30, 6
 );
 
insert into item 
values('026', 'dvd12', 'dvd', 'classical', 'Reed Elsevier	', 2005, 30, 6
 );
 
insert into item 
values('027', 'dvd13', 'dvd', 'new age', 'Reed Elsevier	', 2005, 30, 6
 );
 
insert into item 
values('028', 'dvd14', 'dvd', 'instrumental', 'Reed Elsevier	', 2005, 30, 6
 );

insert into leadsinger 
values('001', 'singer1'
 );
 
insert into leadsinger 
values('002', 'singer2'
 );
 
insert into leadsinger 
values('003', 'singer3'
 );
 
insert into leadsinger 
values('004', 'singer4'
 );
 
insert into leadsinger 
values('005', 'singer5'
 );
 
insert into leadsinger 
values('006', 'singer6'
 );
 
insert into leadsinger 
values('007', 'singer7'
 );
 
insert into hasSong 
values('001', 'cd1'
 );
 
insert into hasSong 
values('005', 'cd2'
 );
 
insert into hasSong 
values('009', 'cd3'
 );
 
insert into hasSong 
values('013', 'cd13'
 );
 
insert into hasSong 
values('017', 'cd14'
 );

insert into hasSong 
values('021', 'cd15'
 );
 
insert into hasSong 
values('025', 'cd16'
 );
 

insert into purchaseItem
values(1, '001', 5	
);

insert into purchaseItem
values(2, '013', 2	
);

insert into purchaseItem
values(6, '020', 7	
);

insert into purchaseItem
values(9, '005', 10	
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

insert into returnItem
values(1, '020', 1 
);

insert into returnItem
values(2, '005', 2
);

commit;
