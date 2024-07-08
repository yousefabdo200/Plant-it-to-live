Create database plant_it_to_live_DB;
use plant_it_to_live_DB;
create table admins
(
	id int auto_increment primary key,
	name Varchar(150) not null,
	email varchar(250) not null ,
	password varchar(1000) not null,
	access_Key varchar(50) not null,
    created_at timestamp,
    updated_at timestamp
);
create table users
(
	id int auto_increment primary key,
	name Varchar(150) ,
	email varchar(250) ,
	password varchar(1000) ,
	phone varchar(20),
    b_date timestamp ,
    gender  BIT,
    picture varchar(300),
    activated bool default false,
	created_at timestamp,
    updated_at timestamp
);
create table plants(
id int auto_increment primary key,
common_name varchar(250),
scientific_name varchar(250),
watering text,
fertilizer text,
sunlight text,
pruning text ,
img varchar(500),
water_amount varchar(500),
fertilizer_amount varchar(500),
sun_per_day varchar(500),
soil_salinty varchar(500),
appropriate_season varchar(150),
admin_id int not null,
foreign key (admin_id)
references admins(id)
);
create table user_plant
(
id int  primary key,
user_id int not null,
plant_id int  not null,
foreign key (user_id)
references users(id),
foreign key (plant_id)
references plants(id)
);
create table Suggested_plants(
id int auto_increment primary key,
common_name varchar(250),
scientific_name varchar(250),
watering text,
fertilizer text,
sunlight text,
pruning text ,
img varchar(500),
water_amount varchar(500),
fertilizer_amount varchar(500),
sun_per_day varchar(500),
soil_salinty varchar(500),
appropriate_season varchar(150),
admin_id int,
user_id int not null,
foreign key (admin_id)
references admins(id),
foreign key (user_id)
references users(id)
);
ALTER TABLE user_plant DROP PRIMARY KEY;
ALTER TABLE user_plant MODIFY COLUMN id INT AUTO_INCREMENT PRIMARY KEY;
alter table Suggested_plants 
add column  approved bit default 0; 
ALTER TABLE Suggested_plants
ADD COLUMN plant_id INT NULL;
ALTER TABLE Suggested_plants
ADD CONSTRAINT unique_plant_id UNIQUE (plant_id);
ALTER TABLE Suggested_plants
ADD CONSTRAINT fk_plant
FOREIGN KEY (plant_id) REFERENCES plants(id);
select * from users;
select * from plants;
select * from admins ;
select * from user_plant;
select * from Suggested_plants;
#ALTER TABLE plants AUTO_INCREMENT = 1;
