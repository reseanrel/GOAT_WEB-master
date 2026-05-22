-- Sample Data: 100 Users + 500 Pets (5 each)
-- Password for all sample users: password123
SET FOREIGN_KEY_CHECKS=0;

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(2, 'Roberto Ramos', 47, '09139043150', '655 Aguinaldo St., Pila, Laguna', 'roberto.ramos1@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Lucy', 'Cat', 'Scottish Fold', 9, 'Yellow', 'Male', 2, 'https://picsum.photos/id/29/300/200', 0, 1, 0, 'approved'),
('Lucy', 'Cat', 'Scottish Fold', 4, 'Brindle', 'Female', 2, 'https://picsum.photos/id/34/300/200', 0, 0, 0, 'approved'),
('Lucy', 'Dog', 'Golden Retriever', 1, 'Yellow', 'Female', 2, 'https://picsum.photos/id/66/300/200', 0, 0, 0, 'approved'),
('Happy', 'Fish', 'Betta', 1, 'Brown', 'Female', 2, 'https://picsum.photos/id/41/300/200', 1, 0, 0, 'approved'),
('Daisy', 'Dog', 'Dachshund', 10, 'Golden', 'Male', 2, 'https://picsum.photos/id/89/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(3, 'Carlos Hernandez', 43, '09473505321', '659 Bonifacio Ave., Pila, Laguna', 'carlos.hernandez2@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Bubbles', 'Cat', 'Ragdoll', 11, 'Spotted', 'Female', 3, 'https://picsum.photos/id/19/300/200', 1, 0, 0, 'approved'),
('Prince', 'Bird', 'Finch', 9, 'Green', 'Female', 3, 'https://picsum.photos/id/19/300/200', 1, 0, 0, 'approved'),
('Duke', 'Cat', 'Scottish Fold', 10, 'Yellow', 'Male', 3, 'https://picsum.photos/id/63/300/200', 1, 0, 0, 'approved'),
('Sparky', 'Bird', 'Cockatiel', 6, 'Spotted', 'Male', 3, 'https://picsum.photos/id/89/300/200', 1, 0, 0, 'approved'),
('Prince', 'Cat', 'Persian', 5, 'Black', 'Female', 3, 'https://picsum.photos/id/48/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(4, 'Jose Torres', 25, '09679083219', '128 Pansol, Pila, Laguna', 'jose.torres3@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Bean', 'Bird', 'Macaw', 12, 'Green', 'Female', 4, 'https://picsum.photos/id/86/300/200', 1, 0, 0, 'approved'),
('Milo', 'Dog', 'Rottweiler', 1, 'Yellow', 'Male', 4, 'https://picsum.photos/id/27/300/200', 0, 0, 0, 'approved'),
('Peanut', 'Cat', 'Puspin', 6, 'Green', 'Male', 4, 'https://picsum.photos/id/86/300/200', 0, 0, 0, 'approved'),
('Buddy', 'Bird', 'Finch', 3, 'White', 'Female', 4, 'https://picsum.photos/id/12/300/200', 1, 0, 0, 'approved'),
('Mittens', 'Rabbit', 'Flemish Giant', 9, 'Blue', 'Female', 4, 'https://picsum.photos/id/77/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(5, 'Patricia Mendoza', 47, '09471752118', '218 Poblacion, Pila, Laguna', 'patricia.mendoza4@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Cookie', 'Dog', 'Golden Retriever', 8, 'Golden', 'Male', 5, 'https://picsum.photos/id/36/300/200', 1, 0, 0, 'approved'),
('King', 'Bird', 'Macaw', 11, 'Golden', 'Male', 5, 'https://picsum.photos/id/61/300/200', 0, 0, 0, 'approved'),
('Goldie', 'Fish', 'Angelfish', 10, 'Gray', 'Female', 5, 'https://picsum.photos/id/43/300/200', 1, 0, 0, 'approved'),
('King', 'Cat', 'Persian', 3, 'Calico', 'Male', 5, 'https://picsum.photos/id/47/300/200', 0, 0, 0, 'approved'),
('Waffles', 'Cat', 'Sphynx', 2, 'Golden', 'Female', 5, 'https://picsum.photos/id/57/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(6, 'Veronica Ramirez', 53, '09258897789', '772 Rizal St., Pila, Laguna', 'veronica.ramirez5@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Milo', 'Cat', 'British Shorthair', 10, 'Brindle', 'Male', 6, 'https://picsum.photos/id/45/300/200', 1, 0, 0, 'approved'),
('Lucy', 'Fish', 'Betta', 3, 'Green', 'Female', 6, 'https://picsum.photos/id/92/300/200', 0, 0, 0, 'approved'),
('Queen', 'Rabbit', 'Flemish Giant', 5, 'Black', 'Male', 6, 'https://picsum.photos/id/45/300/200', 1, 0, 0, 'approved'),
('King', 'Cat', 'Bengal', 8, 'Brindle', 'Male', 6, 'https://picsum.photos/id/35/300/200', 0, 0, 0, 'approved'),
('Princess', 'Cat', 'Puspin', 8, 'Rainbow', 'Female', 6, 'https://picsum.photos/id/54/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(7, 'Carlos Ramirez', 47, '09932344148', '5 Tulay, Pila, Laguna', 'carlos.ramirez6@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Coco', 'Dog', 'Askal', 1, 'Orange', 'Male', 7, 'https://picsum.photos/id/14/300/200', 1, 0, 0, 'approved'),
('Pickles', 'Bird', 'Parakeet', 9, 'Cream', 'Female', 7, 'https://picsum.photos/id/78/300/200', 0, 0, 0, 'approved'),
('Bubbles', 'Dog', 'German Shepherd', 8, 'Brindle', 'Female', 7, 'https://picsum.photos/id/64/300/200', 1, 0, 0, 'approved'),
('Sunny', 'Rabbit', 'Netherland Dwarf', 10, 'Tabby', 'Female', 7, 'https://picsum.photos/id/22/300/200', 0, 0, 0, 'approved'),
('Waffles', 'Rabbit', 'Mini Lop', 9, 'Gray', 'Female', 7, 'https://picsum.photos/id/42/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(8, 'Miguel Reyes', 33, '09926205966', '322 Aguinaldo St., Pila, Laguna', 'miguel.reyes7@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Sparky', 'Fish', 'Angelfish', 2, 'Black', 'Male', 8, 'https://picsum.photos/id/69/300/200', 0, 0, 0, 'approved'),
('Sunny', 'Cat', 'Persian', 12, 'Calico', 'Male', 8, 'https://picsum.photos/id/94/300/200', 1, 0, 0, 'approved'),
('Buddy', 'Dog', 'Aspin', 8, 'Tabby', 'Female', 8, 'https://picsum.photos/id/33/300/200', 1, 0, 0, 'approved'),
('Buddy', 'Bird', 'Parakeet', 1, 'Brown', 'Female', 8, 'https://picsum.photos/id/81/300/200', 1, 0, 0, 'approved'),
('Rocky', 'Cat', 'Domestic Shorthair', 1, 'Yellow', 'Female', 8, 'https://picsum.photos/id/48/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(9, 'Lucia Rodriguez', 27, '09173390039', '52 Pansol, Pila, Laguna', 'lucia.rodriguez8@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Max', 'Fish', 'Discus', 9, 'Calico', 'Female', 9, 'https://picsum.photos/id/39/300/200', 0, 0, 0, 'approved'),
('Prince', 'Bird', 'Finch', 7, 'Yellow', 'Male', 9, 'https://picsum.photos/id/91/300/200', 0, 0, 0, 'approved'),
('Sparky', 'Rabbit', 'Netherland Dwarf', 9, 'Calico', 'Male', 9, 'https://picsum.photos/id/30/300/200', 1, 0, 0, 'approved'),
('Waffles', 'Cat', 'Domestic Shorthair', 12, 'Red', 'Female', 9, 'https://picsum.photos/id/6/300/200', 0, 0, 0, 'approved'),
('Muffin', 'Rabbit', 'Holland Lop', 5, 'Cream', 'Female', 9, 'https://picsum.photos/id/24/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(10, 'Jose Santiago', 52, '09856505290', '681 Tulay, Pila, Laguna', 'jose.santiago9@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Mittens', 'Dog', 'Beagle', 11, 'Black', 'Female', 10, 'https://picsum.photos/id/93/300/200', 0, 0, 0, 'approved'),
('Charlie', 'Dog', 'Aspin', 11, 'Calico', 'Female', 10, 'https://picsum.photos/id/22/300/200', 1, 0, 0, 'approved'),
('Princess', 'Dog', 'Askal', 9, 'Brown', 'Male', 10, 'https://picsum.photos/id/22/300/200', 1, 0, 0, 'approved'),
('Finny', 'Rabbit', 'Netherland Dwarf', 5, 'Green', 'Male', 10, 'https://picsum.photos/id/72/300/200', 0, 0, 0, 'approved'),
('Muffin', 'Fish', 'Discus', 8, 'Rainbow', 'Female', 10, 'https://picsum.photos/id/6/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(11, 'Juan Castro', 44, '09646373989', '277 Pinagbayanan, Pila, Laguna', 'juan.castro10@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Prince', 'Dog', 'Rottweiler', 1, 'Rainbow', 'Male', 11, 'https://picsum.photos/id/93/300/200', 0, 0, 0, 'approved'),
('Daisy', 'Fish', 'Angelfish', 3, 'Golden', 'Female', 11, 'https://picsum.photos/id/5/300/200', 1, 0, 0, 'approved'),
('Buddy', 'Cat', 'Maine Coon', 3, 'Orange', 'Male', 11, 'https://picsum.photos/id/27/300/200', 1, 0, 0, 'approved'),
('Bubbles', 'Rabbit', 'Holland Lop', 1, 'Yellow', 'Female', 11, 'https://picsum.photos/id/15/300/200', 1, 0, 0, 'approved'),
('Shadow', 'Fish', 'Koi', 8, 'White', 'Female', 11, 'https://picsum.photos/id/64/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(12, 'Claudia Lopez', 18, '09494238781', '924 Magsaysay Ave., Pila, Laguna', 'claudia.lopez11@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Charlie', 'Dog', 'Poodle', 8, 'Brown', 'Male', 12, 'https://picsum.photos/id/72/300/200', 0, 0, 0, 'approved'),
('Marshmallow', 'Cat', 'Siamese', 2, 'Yellow', 'Male', 12, 'https://picsum.photos/id/32/300/200', 1, 1, 0, 'approved'),
('Rex', 'Bird', 'Macaw', 2, 'Yellow', 'Female', 12, 'https://picsum.photos/id/12/300/200', 1, 0, 0, 'approved'),
('Shadow', 'Bird', 'Lovebird', 4, 'Orange', 'Male', 12, 'https://picsum.photos/id/57/300/200', 0, 0, 0, 'approved'),
('Angel', 'Dog', 'Bulldog', 1, 'Yellow', 'Male', 12, 'https://picsum.photos/id/93/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(13, 'Andres Cruz', 32, '09194022349', '768 San Roque, Pila, Laguna', 'andres.cruz12@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Muffin', 'Cat', 'Persian', 2, 'Cream', 'Male', 13, 'https://picsum.photos/id/14/300/200', 0, 0, 0, 'approved'),
('Waffles', 'Cat', 'Ragdoll', 7, 'White', 'Female', 13, 'https://picsum.photos/id/50/300/200', 1, 0, 0, 'approved'),
('Jelly', 'Dog', 'Dachshund', 1, 'Rainbow', 'Female', 13, 'https://picsum.photos/id/76/300/200', 1, 0, 0, 'approved'),
('Mittens', 'Bird', 'Parakeet', 9, 'Brindle', 'Male', 13, 'https://picsum.photos/id/23/300/200', 0, 0, 0, 'approved'),
('Charlie', 'Rabbit', 'Mini Lop', 3, 'Gray', 'Female', 13, 'https://picsum.photos/id/34/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(14, 'Elena Martinez', 58, '09506646119', '951 San Miguel, Pila, Laguna', 'elena.martinez13@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Marshmallow', 'Rabbit', 'Flemish Giant', 4, 'Calico', 'Female', 14, 'https://picsum.photos/id/67/300/200', 1, 0, 0, 'approved'),
('Goldie', 'Rabbit', 'Flemish Giant', 10, 'Spotted', 'Male', 14, 'https://picsum.photos/id/51/300/200', 0, 0, 0, 'approved'),
('Mittens', 'Dog', 'Labrador Retriever', 8, 'Yellow', 'Male', 14, 'https://picsum.photos/id/72/300/200', 0, 0, 0, 'approved'),
('Bella', 'Dog', 'German Shepherd', 2, 'Blue', 'Female', 14, 'https://picsum.photos/id/51/300/200', 1, 0, 0, 'approved'),
('Nibbles', 'Rabbit', 'Netherland Dwarf', 6, 'Yellow', 'Female', 14, 'https://picsum.photos/id/48/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(15, 'Miguel Hernandez', 23, '09257657047', '361 Linga, Pila, Laguna', 'miguel.hernandez14@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Brownie', 'Dog', 'Rottweiler', 4, 'Brindle', 'Female', 15, 'https://picsum.photos/id/89/300/200', 1, 0, 0, 'approved'),
('Leo', 'Bird', 'Finch', 4, 'Green', 'Female', 15, 'https://picsum.photos/id/73/300/200', 1, 0, 0, 'approved'),
('Rex', 'Rabbit', 'Netherland Dwarf', 6, 'Spotted', 'Male', 15, 'https://picsum.photos/id/72/300/200', 1, 0, 0, 'approved'),
('Rocky', 'Bird', 'Cockatiel', 4, 'Green', 'Female', 15, 'https://picsum.photos/id/11/300/200', 0, 0, 0, 'approved'),
('Prince', 'Fish', 'Angelfish', 12, 'Calico', 'Female', 15, 'https://picsum.photos/id/99/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(16, 'Carlos Espinosa', 31, '09600889655', '530 Poblacion, Pila, Laguna', 'carlos.espinosa15@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Sunny', 'Dog', 'Dachshund', 1, 'Rainbow', 'Male', 16, 'https://picsum.photos/id/44/300/200', 1, 0, 0, 'approved'),
('King', 'Fish', 'Betta', 11, 'Green', 'Male', 16, 'https://picsum.photos/id/88/300/200', 1, 0, 0, 'approved'),
('Happy', 'Bird', 'Lovebird', 6, 'Golden', 'Female', 16, 'https://picsum.photos/id/51/300/200', 0, 0, 0, 'approved'),
('Queen', 'Dog', 'Beagle', 7, 'Orange', 'Male', 16, 'https://picsum.photos/id/23/300/200', 0, 0, 0, 'approved'),
('Nala', 'Bird', 'Finch', 8, 'Black', 'Male', 16, 'https://picsum.photos/id/64/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(17, 'Luis Delacruz', 19, '09738110378', '766 Bonifacio Ave., Pila, Laguna', 'luis.delacruz16@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Max', 'Fish', 'Koi', 7, 'Tabby', 'Female', 17, 'https://picsum.photos/id/96/300/200', 1, 0, 0, 'approved'),
('Queen', 'Fish', 'Guppy', 9, 'Blue', 'Male', 17, 'https://picsum.photos/id/79/300/200', 1, 0, 0, 'approved'),
('Leo', 'Bird', 'Cockatiel', 9, 'Golden', 'Female', 17, 'https://picsum.photos/id/42/300/200', 1, 0, 0, 'approved'),
('Marshmallow', 'Fish', 'Goldfish', 7, 'Blue', 'Male', 17, 'https://picsum.photos/id/78/300/200', 0, 0, 0, 'approved'),
('Jelly', 'Bird', 'Finch', 11, 'Golden', 'Female', 17, 'https://picsum.photos/id/43/300/200', 1, 1, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(18, 'Gabriela Martinez', 29, '09859251734', '625 Tulay, Pila, Laguna', 'gabriela.martinez17@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Lucky', 'Cat', 'Sphynx', 12, 'Cream', 'Male', 18, 'https://picsum.photos/id/88/300/200', 1, 0, 0, 'approved'),
('Queen', 'Fish', 'Angelfish', 4, 'Gray', 'Female', 18, 'https://picsum.photos/id/44/300/200', 1, 0, 0, 'approved'),
('Peanut', 'Bird', 'Finch', 1, 'Blue', 'Female', 18, 'https://picsum.photos/id/84/300/200', 0, 0, 0, 'approved'),
('Polly', 'Dog', 'Bulldog', 8, 'White', 'Male', 18, 'https://picsum.photos/id/76/300/200', 1, 0, 0, 'approved'),
('Luna', 'Cat', 'Ragdoll', 11, 'Golden', 'Male', 18, 'https://picsum.photos/id/35/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(19, 'Ricardo Domingo', 18, '09609423394', '286 Magsaysay Ave., Pila, Laguna', 'ricardo.domingo18@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Bubbles', 'Fish', 'Angelfish', 1, 'Spotted', 'Male', 19, 'https://picsum.photos/id/92/300/200', 1, 0, 0, 'approved'),
('Daisy', 'Bird', 'Lovebird', 9, 'Black', 'Male', 19, 'https://picsum.photos/id/42/300/200', 0, 0, 0, 'approved'),
('Duke', 'Bird', 'African Grey', 11, 'Rainbow', 'Male', 19, 'https://picsum.photos/id/37/300/200', 1, 0, 0, 'approved'),
('Muffin', 'Fish', 'Koi', 11, 'White', 'Female', 19, 'https://picsum.photos/id/86/300/200', 1, 0, 0, 'approved'),
('Oreo', 'Rabbit', 'Rex', 5, 'Orange', 'Male', 19, 'https://picsum.photos/id/74/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(20, 'Gabriela Reyes', 62, '09921947930', '745 San Miguel, Pila, Laguna', 'gabriela.reyes19@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Prince', 'Dog', 'Beagle', 6, 'Tabby', 'Female', 20, 'https://picsum.photos/id/22/300/200', 0, 0, 0, 'approved'),
('Peanut', 'Bird', 'Parakeet', 9, 'Blue', 'Male', 20, 'https://picsum.photos/id/59/300/200', 1, 0, 0, 'approved'),
('Nibbles', 'Fish', 'Angelfish', 11, 'White', 'Male', 20, 'https://picsum.photos/id/63/300/200', 0, 0, 0, 'approved'),
('Sparky', 'Fish', 'Guppy', 11, 'Gray', 'Female', 20, 'https://picsum.photos/id/92/300/200', 0, 0, 0, 'approved'),
('Princess', 'Cat', 'British Shorthair', 8, 'Orange', 'Male', 20, 'https://picsum.photos/id/96/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(21, 'Raul Gonzales', 40, '09309256646', '609 Bonifacio Ave., Pila, Laguna', 'raul.gonzales20@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Nibbles', 'Rabbit', 'Mini Lop', 10, 'Golden', 'Female', 21, 'https://picsum.photos/id/74/300/200', 1, 0, 0, 'approved'),
('Prince', 'Bird', 'Parakeet', 4, 'Brindle', 'Male', 21, 'https://picsum.photos/id/64/300/200', 0, 0, 0, 'approved'),
('Queen', 'Fish', 'Goldfish', 6, 'Black', 'Female', 21, 'https://picsum.photos/id/1/300/200', 0, 0, 0, 'approved'),
('Angel', 'Fish', 'Goldfish', 8, 'Green', 'Female', 21, 'https://picsum.photos/id/44/300/200', 1, 0, 0, 'approved'),
('Hopper', 'Dog', 'Labrador Retriever', 8, 'Calico', 'Male', 21, 'https://picsum.photos/id/93/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(22, 'Fernando Francisco', 34, '09719251040', '436 Bulilan, Pila, Laguna', 'fernando.francisco21@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Nala', 'Rabbit', 'Flemish Giant', 4, 'Gray', 'Male', 22, 'https://picsum.photos/id/41/300/200', 0, 0, 0, 'approved'),
('Thumper', 'Fish', 'Guppy', 10, 'Orange', 'Female', 22, 'https://picsum.photos/id/55/300/200', 1, 0, 0, 'approved'),
('Cookie', 'Fish', 'Discus', 1, 'Rainbow', 'Male', 22, 'https://picsum.photos/id/82/300/200', 0, 0, 0, 'approved'),
('Simba', 'Bird', 'Finch', 8, 'Calico', 'Male', 22, 'https://picsum.photos/id/62/300/200', 0, 0, 0, 'approved'),
('Queen', 'Dog', 'Shih Tzu', 6, 'Red', 'Male', 22, 'https://picsum.photos/id/9/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(23, 'Maria Delacruz', 49, '09424069430', '265 Bulilan, Pila, Laguna', 'maria.delacruz22@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Marshmallow', 'Cat', 'Domestic Shorthair', 7, 'Spotted', 'Male', 23, 'https://picsum.photos/id/44/300/200', 0, 0, 0, 'approved'),
('Whiskers', 'Dog', 'Bulldog', 3, 'Calico', 'Female', 23, 'https://picsum.photos/id/61/300/200', 0, 0, 0, 'approved'),
('Max', 'Fish', 'Betta', 11, 'Cream', 'Female', 23, 'https://picsum.photos/id/37/300/200', 0, 0, 0, 'approved'),
('Pudding', 'Rabbit', 'Mini Lop', 7, 'Tabby', 'Male', 23, 'https://picsum.photos/id/33/300/200', 1, 0, 0, 'approved'),
('Nala', 'Rabbit', 'Rex', 1, 'Calico', 'Male', 23, 'https://picsum.photos/id/47/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(24, 'Luis Martinez', 42, '09421263117', '160 Mabini St., Pila, Laguna', 'luis.martinez23@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Tweety', 'Bird', 'Parakeet', 3, 'Cream', 'Female', 24, 'https://picsum.photos/id/47/300/200', 0, 0, 0, 'approved'),
('Jelly', 'Rabbit', 'Netherland Dwarf', 9, 'Blue', 'Male', 24, 'https://picsum.photos/id/54/300/200', 1, 0, 0, 'approved'),
('Mittens', 'Rabbit', 'Netherland Dwarf', 3, 'Golden', 'Female', 24, 'https://picsum.photos/id/8/300/200', 1, 0, 0, 'approved'),
('Shadow', 'Bird', 'Finch', 10, 'Yellow', 'Female', 24, 'https://picsum.photos/id/34/300/200', 0, 1, 0, 'approved'),
('Fluffy', 'Rabbit', 'Flemish Giant', 6, 'Spotted', 'Male', 24, 'https://picsum.photos/id/48/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(25, 'Claudia Villanueva', 36, '09638431258', '254 Mojon, Pila, Laguna', 'claudia.villanueva24@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Duke', 'Fish', 'Angelfish', 8, 'Tabby', 'Female', 25, 'https://picsum.photos/id/7/300/200', 1, 0, 0, 'approved'),
('Finny', 'Dog', 'Beagle', 2, 'Calico', 'Male', 25, 'https://picsum.photos/id/78/300/200', 0, 0, 0, 'approved'),
('Sparky', 'Fish', 'Discus', 6, 'Brown', 'Female', 25, 'https://picsum.photos/id/94/300/200', 1, 0, 0, 'approved'),
('Queen', 'Rabbit', 'Rex', 5, 'Rainbow', 'Male', 25, 'https://picsum.photos/id/26/300/200', 0, 0, 0, 'approved'),
('Tweety', 'Dog', 'Aspin', 4, 'Yellow', 'Female', 25, 'https://picsum.photos/id/95/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(26, 'Luis Ramos', 58, '09585929267', '505 Mabini St., Pila, Laguna', 'luis.ramos25@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Marshmallow', 'Cat', 'Scottish Fold', 3, 'Spotted', 'Male', 26, 'https://picsum.photos/id/87/300/200', 1, 0, 0, 'approved'),
('Hopper', 'Rabbit', 'Flemish Giant', 7, 'Yellow', 'Male', 26, 'https://picsum.photos/id/33/300/200', 1, 0, 0, 'approved'),
('Angel', 'Rabbit', 'Flemish Giant', 6, 'White', 'Male', 26, 'https://picsum.photos/id/48/300/200', 0, 0, 0, 'approved'),
('Shadow', 'Rabbit', 'Flemish Giant', 11, 'Gray', 'Female', 26, 'https://picsum.photos/id/78/300/200', 0, 0, 0, 'approved'),
('Brownie', 'Fish', 'Guppy', 7, 'Black', 'Male', 26, 'https://picsum.photos/id/81/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(27, 'Claudia Flores', 29, '09209944695', '240 Mojon, Pila, Laguna', 'claudia.flores26@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Peanut', 'Rabbit', 'Rex', 12, 'Black', 'Female', 27, 'https://picsum.photos/id/53/300/200', 0, 0, 0, 'approved'),
('Muffin', 'Bird', 'Macaw', 7, 'Brown', 'Female', 27, 'https://picsum.photos/id/49/300/200', 0, 0, 0, 'approved'),
('Buddy', 'Fish', 'Guppy', 12, 'Yellow', 'Female', 27, 'https://picsum.photos/id/3/300/200', 1, 0, 0, 'approved'),
('Rex', 'Rabbit', 'Mini Lop', 8, 'Gray', 'Female', 27, 'https://picsum.photos/id/92/300/200', 0, 0, 0, 'approved'),
('Mochi', 'Rabbit', 'Holland Lop', 11, 'Gray', 'Female', 27, 'https://picsum.photos/id/88/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(28, 'Gabriela Francisco', 25, '09190955089', '34 Mojon, Pila, Laguna', 'gabriela.francisco27@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Fluffy', 'Cat', 'Persian', 11, 'Tabby', 'Male', 28, 'https://picsum.photos/id/64/300/200', 0, 0, 0, 'approved'),
('Finny', 'Cat', 'Siamese', 1, 'Calico', 'Female', 28, 'https://picsum.photos/id/36/300/200', 1, 0, 0, 'approved'),
('Lucy', 'Cat', 'Sphynx', 5, 'Spotted', 'Male', 28, 'https://picsum.photos/id/41/300/200', 1, 0, 0, 'approved'),
('Simba', 'Fish', 'Goldfish', 11, 'Gray', 'Female', 28, 'https://picsum.photos/id/43/300/200', 0, 0, 0, 'approved'),
('Finny', 'Fish', 'Discus', 6, 'Tabby', 'Male', 28, 'https://picsum.photos/id/66/300/200', 0, 1, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(29, 'Miguel Mendoza', 27, '09436805365', '655 San Miguel, Pila, Laguna', 'miguel.mendoza28@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Sunny', 'Dog', 'Pomeranian', 1, 'Blue', 'Female', 29, 'https://picsum.photos/id/61/300/200', 1, 0, 0, 'approved'),
('Finny', 'Fish', 'Discus', 10, 'Spotted', 'Female', 29, 'https://picsum.photos/id/63/300/200', 1, 0, 0, 'approved'),
('Bean', 'Dog', 'Poodle', 1, 'Red', 'Male', 29, 'https://picsum.photos/id/36/300/200', 1, 0, 0, 'approved'),
('Bella', 'Bird', 'Macaw', 12, 'Blue', 'Male', 29, 'https://picsum.photos/id/17/300/200', 1, 0, 0, 'approved'),
('Finny', 'Cat', 'Persian', 2, 'Cream', 'Female', 29, 'https://picsum.photos/id/22/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(30, 'Daniela Navarro', 56, '09371356582', '17 Mojon, Pila, Laguna', 'daniela.navarro29@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Nibbles', 'Fish', 'Koi', 4, 'Gray', 'Male', 30, 'https://picsum.photos/id/64/300/200', 1, 0, 0, 'approved'),
('Fluffy', 'Bird', 'Cockatiel', 1, 'Black', 'Male', 30, 'https://picsum.photos/id/14/300/200', 1, 0, 0, 'approved'),
('Polly', 'Fish', 'Goldfish', 10, 'Brindle', 'Female', 30, 'https://picsum.photos/id/63/300/200', 0, 0, 0, 'approved'),
('Lucy', 'Dog', 'Aspin', 1, 'Blue', 'Female', 30, 'https://picsum.photos/id/18/300/200', 0, 1, 0, 'approved'),
('Daisy', 'Dog', 'Golden Retriever', 5, 'Spotted', 'Female', 30, 'https://picsum.photos/id/47/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(31, 'Camila Villar', 55, '09425008169', '544 Luna St., Pila, Laguna', 'camila.villar30@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Princess', 'Fish', 'Koi', 10, 'Tabby', 'Male', 31, 'https://picsum.photos/id/47/300/200', 0, 0, 0, 'approved'),
('Sparky', 'Bird', 'Canary', 2, 'Tabby', 'Female', 31, 'https://picsum.photos/id/20/300/200', 1, 0, 0, 'approved'),
('Rex', 'Cat', 'Bengal', 1, 'Gray', 'Male', 31, 'https://picsum.photos/id/86/300/200', 1, 0, 0, 'approved'),
('Buddy', 'Rabbit', 'Holland Lop', 11, 'Yellow', 'Male', 31, 'https://picsum.photos/id/95/300/200', 1, 0, 0, 'approved'),
('Thumper', 'Bird', 'Finch', 5, 'Brindle', 'Female', 31, 'https://picsum.photos/id/16/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(32, 'Juan Villanueva', 28, '09784242154', '840 Rizal St., Pila, Laguna', 'juan.villanueva31@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Pancake', 'Dog', 'Poodle', 6, 'Cream', 'Male', 32, 'https://picsum.photos/id/38/300/200', 0, 0, 0, 'approved'),
('Brownie', 'Bird', 'Lovebird', 8, 'Calico', 'Male', 32, 'https://picsum.photos/id/1/300/200', 1, 0, 0, 'approved'),
('Rex', 'Dog', 'Aspin', 6, 'Green', 'Male', 32, 'https://picsum.photos/id/65/300/200', 1, 0, 0, 'approved'),
('Whiskers', 'Fish', 'Betta', 7, 'Green', 'Male', 32, 'https://picsum.photos/id/44/300/200', 1, 0, 0, 'approved'),
('Prince', 'Fish', 'Koi', 8, 'Tabby', 'Female', 32, 'https://picsum.photos/id/31/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(33, 'Roberto Flores', 57, '09976492667', '227 Mojon, Pila, Laguna', 'roberto.flores32@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Simba', 'Bird', 'Parakeet', 3, 'Black', 'Male', 33, 'https://picsum.photos/id/34/300/200', 1, 0, 0, 'approved'),
('Mittens', 'Bird', 'Canary', 3, 'Tabby', 'Female', 33, 'https://picsum.photos/id/69/300/200', 0, 0, 0, 'approved'),
('Tweety', 'Fish', 'Discus', 1, 'Green', 'Female', 33, 'https://picsum.photos/id/6/300/200', 0, 0, 0, 'approved'),
('Jelly', 'Bird', 'Cockatiel', 9, 'Golden', 'Male', 33, 'https://picsum.photos/id/46/300/200', 1, 0, 0, 'approved'),
('Prince', 'Fish', 'Betta', 1, 'Brindle', 'Male', 33, 'https://picsum.photos/id/92/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(34, 'Miguel Gonzales', 18, '09400643102', '173 Bonifacio Ave., Pila, Laguna', 'miguel.gonzales33@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Sunny', 'Fish', 'Angelfish', 1, 'Brindle', 'Male', 34, 'https://picsum.photos/id/24/300/200', 0, 0, 0, 'approved'),
('Shadow', 'Fish', 'Angelfish', 7, 'White', 'Male', 34, 'https://picsum.photos/id/23/300/200', 1, 0, 0, 'approved'),
('Marshmallow', 'Bird', 'African Grey', 8, 'White', 'Male', 34, 'https://picsum.photos/id/55/300/200', 0, 0, 0, 'approved'),
('Mochi', 'Cat', 'Sphynx', 10, 'Orange', 'Male', 34, 'https://picsum.photos/id/82/300/200', 0, 0, 0, 'approved'),
('Shadow', 'Cat', 'British Shorthair', 6, 'Orange', 'Female', 34, 'https://picsum.photos/id/29/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(35, 'Andres Francisco', 64, '09826940312', '888 Aplaya, Pila, Laguna', 'andres.francisco34@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Lucy', 'Bird', 'Macaw', 10, 'Spotted', 'Female', 35, 'https://picsum.photos/id/31/300/200', 0, 0, 0, 'approved'),
('Cookie', 'Cat', 'Ragdoll', 7, 'Yellow', 'Male', 35, 'https://picsum.photos/id/62/300/200', 0, 0, 0, 'approved'),
('Buddy', 'Bird', 'Macaw', 1, 'Rainbow', 'Male', 35, 'https://picsum.photos/id/54/300/200', 1, 0, 0, 'approved'),
('Pickles', 'Bird', 'Parakeet', 12, 'Spotted', 'Female', 35, 'https://picsum.photos/id/51/300/200', 1, 0, 0, 'approved'),
('Finny', 'Dog', 'Aspin', 10, 'Brindle', 'Female', 35, 'https://picsum.photos/id/66/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(36, 'Roberto Rivera', 21, '09767590178', '841 San Roque, Pila, Laguna', 'roberto.rivera35@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Princess', 'Dog', 'Shih Tzu', 2, 'Calico', 'Male', 36, 'https://picsum.photos/id/45/300/200', 1, 0, 0, 'approved'),
('Buddy', 'Dog', 'Pomeranian', 11, 'Rainbow', 'Male', 36, 'https://picsum.photos/id/96/300/200', 1, 0, 0, 'approved'),
('Oreo', 'Rabbit', 'Rex', 11, 'White', 'Male', 36, 'https://picsum.photos/id/16/300/200', 0, 0, 0, 'approved'),
('Rex', 'Cat', 'Puspin', 7, 'Rainbow', 'Female', 36, 'https://picsum.photos/id/84/300/200', 0, 0, 0, 'approved'),
('Rocky', 'Cat', 'Sphynx', 1, 'Tabby', 'Male', 36, 'https://picsum.photos/id/93/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(37, 'Claudia Flores', 29, '09339496831', '206 Quezon Blvd., Pila, Laguna', 'claudia.flores36@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Lucky', 'Rabbit', 'Rex', 8, 'Green', 'Male', 37, 'https://picsum.photos/id/18/300/200', 1, 0, 0, 'approved'),
('Tweety', 'Bird', 'Finch', 1, 'Red', 'Male', 37, 'https://picsum.photos/id/29/300/200', 0, 0, 0, 'approved'),
('Whiskers', 'Rabbit', 'Mini Lop', 5, 'Brindle', 'Female', 37, 'https://picsum.photos/id/47/300/200', 1, 0, 0, 'approved'),
('Bean', 'Dog', 'Bulldog', 3, 'Rainbow', 'Female', 37, 'https://picsum.photos/id/50/300/200', 0, 0, 0, 'approved'),
('Prince', 'Bird', 'Macaw', 11, 'Cream', 'Male', 37, 'https://picsum.photos/id/49/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(38, 'Laura Villanueva', 20, '09147015556', '232 Bonifacio Ave., Pila, Laguna', 'laura.villanueva37@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Charlie', 'Cat', 'Scottish Fold', 5, 'Cream', 'Male', 38, 'https://picsum.photos/id/71/300/200', 1, 0, 0, 'approved'),
('Bubbles', 'Cat', 'British Shorthair', 2, 'Cream', 'Female', 38, 'https://picsum.photos/id/92/300/200', 1, 0, 0, 'approved'),
('Leo', 'Fish', 'Goldfish', 11, 'Golden', 'Male', 38, 'https://picsum.photos/id/1/300/200', 1, 0, 0, 'approved'),
('Pancake', 'Bird', 'African Grey', 12, 'Yellow', 'Male', 38, 'https://picsum.photos/id/14/300/200', 0, 0, 0, 'approved'),
('Hopper', 'Dog', 'Poodle', 12, 'Calico', 'Male', 38, 'https://picsum.photos/id/38/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(39, 'Isabella Villanueva', 37, '09394350173', '595 Mabini St., Pila, Laguna', 'isabella.villanueva38@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Lucky', 'Dog', 'Aspin', 8, 'Orange', 'Female', 39, 'https://picsum.photos/id/60/300/200', 1, 0, 0, 'approved'),
('Hopper', 'Cat', 'Ragdoll', 3, 'Brown', 'Female', 39, 'https://picsum.photos/id/96/300/200', 0, 0, 0, 'approved'),
('Nibbles', 'Fish', 'Angelfish', 5, 'Brindle', 'Male', 39, 'https://picsum.photos/id/68/300/200', 1, 0, 0, 'approved'),
('Rex', 'Rabbit', 'Rex', 8, 'Gray', 'Male', 39, 'https://picsum.photos/id/49/300/200', 0, 0, 0, 'approved'),
('Mochi', 'Dog', 'Aspin', 10, 'Golden', 'Male', 39, 'https://picsum.photos/id/35/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(40, 'Carmen Diaz', 64, '09782408304', '323 San Roque, Pila, Laguna', 'carmen.diaz39@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Princess', 'Bird', 'Parakeet', 4, 'Red', 'Male', 40, 'https://picsum.photos/id/5/300/200', 1, 1, 0, 'approved'),
('Rex', 'Bird', 'Lovebird', 1, 'Yellow', 'Male', 40, 'https://picsum.photos/id/66/300/200', 0, 0, 0, 'approved'),
('Cupcake', 'Fish', 'Betta', 10, 'Blue', 'Female', 40, 'https://picsum.photos/id/30/300/200', 0, 0, 0, 'approved'),
('Oreo', 'Bird', 'Canary', 6, 'Red', 'Female', 40, 'https://picsum.photos/id/45/300/200', 0, 0, 0, 'approved'),
('Milo', 'Rabbit', 'Rex', 4, 'Red', 'Female', 40, 'https://picsum.photos/id/5/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(41, 'Raul Morales', 22, '09442279140', '560 Pansol, Pila, Laguna', 'raul.morales40@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Prince', 'Cat', 'Domestic Shorthair', 10, 'Rainbow', 'Male', 41, 'https://picsum.photos/id/85/300/200', 1, 0, 0, 'approved'),
('Finny', 'Rabbit', 'Mini Lop', 4, 'Brindle', 'Female', 41, 'https://picsum.photos/id/28/300/200', 1, 0, 0, 'approved'),
('Waffles', 'Fish', 'Koi', 7, 'Yellow', 'Female', 41, 'https://picsum.photos/id/75/300/200', 1, 1, 0, 'approved'),
('Hopper', 'Cat', 'Siamese', 7, 'Black', 'Male', 41, 'https://picsum.photos/id/51/300/200', 1, 0, 0, 'approved'),
('Pancake', 'Rabbit', 'Mini Lop', 2, 'Black', 'Female', 41, 'https://picsum.photos/id/96/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(42, 'Jose Torres', 60, '09397352480', '156 Magsaysay Ave., Pila, Laguna', 'jose.torres41@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Peanut', 'Fish', 'Discus', 5, 'Tabby', 'Female', 42, 'https://picsum.photos/id/19/300/200', 0, 0, 0, 'approved'),
('Rex', 'Bird', 'Canary', 6, 'Black', 'Male', 42, 'https://picsum.photos/id/16/300/200', 1, 0, 0, 'approved'),
('Simba', 'Rabbit', 'Netherland Dwarf', 9, 'Brindle', 'Female', 42, 'https://picsum.photos/id/7/300/200', 0, 0, 0, 'approved'),
('Pickles', 'Cat', 'Scottish Fold', 12, 'Black', 'Female', 42, 'https://picsum.photos/id/63/300/200', 1, 0, 0, 'approved'),
('Prince', 'Fish', 'Guppy', 5, 'Cream', 'Male', 42, 'https://picsum.photos/id/73/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(43, 'Fernando Delacruz', 44, '09262815794', '510 Mojon, Pila, Laguna', 'fernando.delacruz42@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Prince', 'Dog', 'Pomeranian', 2, 'Brown', 'Male', 43, 'https://picsum.photos/id/47/300/200', 1, 0, 0, 'approved'),
('Coco', 'Bird', 'Lovebird', 9, 'Brindle', 'Male', 43, 'https://picsum.photos/id/89/300/200', 0, 0, 0, 'approved'),
('Bella', 'Rabbit', 'Netherland Dwarf', 2, 'Brown', 'Male', 43, 'https://picsum.photos/id/98/300/200', 0, 0, 0, 'approved'),
('Finny', 'Fish', 'Angelfish', 4, 'Yellow', 'Male', 43, 'https://picsum.photos/id/90/300/200', 1, 0, 0, 'approved'),
('Mochi', 'Dog', 'Poodle', 3, 'Rainbow', 'Female', 43, 'https://picsum.photos/id/7/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(44, 'Daniela Francisco', 45, '09708213148', '724 Magsaysay Ave., Pila, Laguna', 'daniela.francisco43@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Nibbles', 'Rabbit', 'Netherland Dwarf', 11, 'Cream', 'Male', 44, 'https://picsum.photos/id/54/300/200', 1, 0, 0, 'approved'),
('Princess', 'Cat', 'Persian', 6, 'Red', 'Male', 44, 'https://picsum.photos/id/29/300/200', 0, 0, 0, 'approved'),
('Pudding', 'Dog', 'Poodle', 1, 'Red', 'Male', 44, 'https://picsum.photos/id/59/300/200', 1, 0, 0, 'approved'),
('Coco', 'Bird', 'Lovebird', 9, 'Gray', 'Female', 44, 'https://picsum.photos/id/6/300/200', 0, 0, 0, 'approved'),
('Bean', 'Cat', 'British Shorthair', 1, 'Golden', 'Male', 44, 'https://picsum.photos/id/24/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(45, 'Maria Ocampo', 55, '09105457497', '415 Luna St., Pila, Laguna', 'maria.ocampo44@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Sparky', 'Dog', 'German Shepherd', 8, 'Brown', 'Female', 45, 'https://picsum.photos/id/47/300/200', 0, 1, 0, 'approved'),
('Waffles', 'Dog', 'Aspin', 7, 'Spotted', 'Female', 45, 'https://picsum.photos/id/28/300/200', 1, 0, 0, 'approved'),
('Tweety', 'Cat', 'Domestic Shorthair', 7, 'Blue', 'Male', 45, 'https://picsum.photos/id/94/300/200', 1, 0, 0, 'approved'),
('Marshmallow', 'Bird', 'Macaw', 9, 'Brindle', 'Male', 45, 'https://picsum.photos/id/50/300/200', 1, 0, 0, 'approved'),
('Leo', 'Cat', 'Bengal', 5, 'Brown', 'Male', 45, 'https://picsum.photos/id/50/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(46, 'Diego Bautista', 61, '09361267758', '371 San Roque, Pila, Laguna', 'diego.bautista45@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Milo', 'Rabbit', 'Netherland Dwarf', 1, 'Rainbow', 'Male', 46, 'https://picsum.photos/id/48/300/200', 1, 0, 0, 'approved'),
('Pancake', 'Fish', 'Guppy', 9, 'Brown', 'Female', 46, 'https://picsum.photos/id/56/300/200', 0, 0, 0, 'approved'),
('Duke', 'Rabbit', 'Mini Lop', 2, 'Tabby', 'Female', 46, 'https://picsum.photos/id/27/300/200', 1, 0, 0, 'approved'),
('Sparky', 'Cat', 'Persian', 8, 'Brindle', 'Female', 46, 'https://picsum.photos/id/56/300/200', 0, 0, 0, 'approved'),
('Cupcake', 'Bird', 'Cockatiel', 12, 'White', 'Female', 46, 'https://picsum.photos/id/67/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(47, 'Valentina Bautista', 33, '09932532978', '712 Luna St., Pila, Laguna', 'valentina.bautista46@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Lucky', 'Fish', 'Koi', 4, 'Spotted', 'Female', 47, 'https://picsum.photos/id/31/300/200', 1, 0, 0, 'approved'),
('Hopper', 'Cat', 'British Shorthair', 11, 'Calico', 'Female', 47, 'https://picsum.photos/id/50/300/200', 0, 0, 0, 'approved'),
('Sunny', 'Bird', 'Lovebird', 9, 'Spotted', 'Male', 47, 'https://picsum.photos/id/3/300/200', 1, 0, 0, 'approved'),
('Lucy', 'Fish', 'Guppy', 1, 'Rainbow', 'Female', 47, 'https://picsum.photos/id/84/300/200', 1, 0, 0, 'approved'),
('Simba', 'Cat', 'Maine Coon', 10, 'Orange', 'Female', 47, 'https://picsum.photos/id/43/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(48, 'Juan Garcia', 60, '09922307198', '751 San Antonio, Pila, Laguna', 'juan.garcia47@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Milo', 'Bird', 'Macaw', 12, 'Spotted', 'Female', 48, 'https://picsum.photos/id/57/300/200', 1, 0, 0, 'approved'),
('Angel', 'Dog', 'Bulldog', 7, 'Golden', 'Female', 48, 'https://picsum.photos/id/74/300/200', 1, 0, 0, 'approved'),
('Lucky', 'Rabbit', 'Netherland Dwarf', 11, 'Brindle', 'Male', 48, 'https://picsum.photos/id/83/300/200', 1, 0, 0, 'approved'),
('Queen', 'Cat', 'Scottish Fold', 7, 'Gray', 'Female', 48, 'https://picsum.photos/id/79/300/200', 1, 1, 0, 'approved'),
('Sparky', 'Bird', 'Canary', 9, 'Brindle', 'Male', 48, 'https://picsum.photos/id/19/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(49, 'Juan Villanueva', 23, '09661449021', '753 Mojon, Pila, Laguna', 'juan.villanueva48@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Happy', 'Fish', 'Angelfish', 5, 'Tabby', 'Male', 49, 'https://picsum.photos/id/23/300/200', 0, 0, 0, 'approved'),
('Rocky', 'Cat', 'Sphynx', 4, 'Cream', 'Male', 49, 'https://picsum.photos/id/55/300/200', 0, 0, 0, 'approved'),
('Whiskers', 'Rabbit', 'Holland Lop', 8, 'Red', 'Male', 49, 'https://picsum.photos/id/69/300/200', 0, 0, 0, 'approved'),
('Leo', 'Fish', 'Guppy', 3, 'Cream', 'Male', 49, 'https://picsum.photos/id/40/300/200', 0, 0, 0, 'approved'),
('Peanut', 'Cat', 'Puspin', 3, 'Blue', 'Female', 49, 'https://picsum.photos/id/61/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(50, 'Carmen Villar', 52, '09705991549', '314 Quezon Blvd., Pila, Laguna', 'carmen.villar49@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Cupcake', 'Fish', 'Angelfish', 7, 'Tabby', 'Male', 50, 'https://picsum.photos/id/57/300/200', 0, 0, 0, 'approved'),
('Bella', 'Rabbit', 'Flemish Giant', 3, 'Golden', 'Male', 50, 'https://picsum.photos/id/40/300/200', 1, 0, 0, 'approved'),
('Pickles', 'Bird', 'Macaw', 5, 'Rainbow', 'Male', 50, 'https://picsum.photos/id/30/300/200', 1, 0, 0, 'approved'),
('Cupcake', 'Fish', 'Angelfish', 8, 'Gray', 'Female', 50, 'https://picsum.photos/id/80/300/200', 0, 0, 0, 'approved'),
('Princess', 'Fish', 'Discus', 11, 'Orange', 'Female', 50, 'https://picsum.photos/id/15/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(51, 'Elena Aquino', 50, '09583547635', '744 Poblacion, Pila, Laguna', 'elena.aquino50@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Cupcake', 'Dog', 'Askal', 6, 'Orange', 'Male', 51, 'https://picsum.photos/id/71/300/200', 0, 0, 0, 'approved'),
('Mittens', 'Dog', 'Pomeranian', 5, 'White', 'Male', 51, 'https://picsum.photos/id/94/300/200', 0, 0, 0, 'approved'),
('Simba', 'Bird', 'African Grey', 3, 'Golden', 'Female', 51, 'https://picsum.photos/id/29/300/200', 0, 0, 0, 'approved'),
('Bubbles', 'Fish', 'Discus', 2, 'Gray', 'Male', 51, 'https://picsum.photos/id/62/300/200', 0, 0, 0, 'approved'),
('Peanut', 'Fish', 'Goldfish', 6, 'Spotted', 'Female', 51, 'https://picsum.photos/id/90/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(52, 'Ana Villar', 47, '09245591959', '779 Niyugan, Pila, Laguna', 'ana.villar51@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Pickles', 'Dog', 'Aspin', 8, 'Rainbow', 'Female', 52, 'https://picsum.photos/id/86/300/200', 1, 0, 0, 'approved'),
('Nibbles', 'Cat', 'Persian', 8, 'Calico', 'Female', 52, 'https://picsum.photos/id/18/300/200', 0, 0, 0, 'approved'),
('Bean', 'Rabbit', 'Rex', 9, 'White', 'Female', 52, 'https://picsum.photos/id/55/300/200', 0, 0, 0, 'approved'),
('Max', 'Cat', 'British Shorthair', 9, 'Brindle', 'Female', 52, 'https://picsum.photos/id/52/300/200', 0, 0, 0, 'approved'),
('Bella', 'Bird', 'Parakeet', 6, 'Gray', 'Male', 52, 'https://picsum.photos/id/93/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(53, 'Veronica Aquino', 55, '09898237948', '70 Poblacion, Pila, Laguna', 'veronica.aquino52@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Duke', 'Bird', 'Lovebird', 12, 'Brindle', 'Female', 53, 'https://picsum.photos/id/52/300/200', 1, 1, 0, 'approved'),
('Luna', 'Fish', 'Koi', 9, 'Black', 'Female', 53, 'https://picsum.photos/id/58/300/200', 0, 0, 0, 'approved'),
('Hopper', 'Fish', 'Guppy', 12, 'Black', 'Female', 53, 'https://picsum.photos/id/72/300/200', 0, 0, 0, 'approved'),
('Leo', 'Rabbit', 'Mini Lop', 5, 'Brindle', 'Female', 53, 'https://picsum.photos/id/61/300/200', 0, 0, 0, 'approved'),
('Bubbles', 'Cat', 'Puspin', 3, 'Brown', 'Female', 53, 'https://picsum.photos/id/25/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(54, 'Raul Cruz', 39, '09975485142', '946 Bulilan, Pila, Laguna', 'raul.cruz53@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Brownie', 'Fish', 'Betta', 1, 'Green', 'Male', 54, 'https://picsum.photos/id/15/300/200', 1, 0, 0, 'approved'),
('Goldie', 'Bird', 'African Grey', 5, 'Green', 'Female', 54, 'https://picsum.photos/id/38/300/200', 1, 0, 0, 'approved'),
('Pudding', 'Fish', 'Goldfish', 1, 'Brindle', 'Female', 54, 'https://picsum.photos/id/61/300/200', 0, 0, 0, 'approved'),
('Bean', 'Cat', 'British Shorthair', 3, 'Tabby', 'Female', 54, 'https://picsum.photos/id/70/300/200', 1, 0, 0, 'approved'),
('Oreo', 'Rabbit', 'Flemish Giant', 6, 'Spotted', 'Female', 54, 'https://picsum.photos/id/99/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(55, 'Roberto Domingo', 21, '09674305484', '155 Luna St., Pila, Laguna', 'roberto.domingo54@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Sunny', 'Dog', 'Aspin', 9, 'Brindle', 'Male', 55, 'https://picsum.photos/id/13/300/200', 0, 0, 0, 'approved'),
('Fluffy', 'Dog', 'Aspin', 9, 'Tabby', 'Male', 55, 'https://picsum.photos/id/24/300/200', 0, 0, 0, 'approved'),
('Shadow', 'Fish', 'Goldfish', 12, 'Black', 'Male', 55, 'https://picsum.photos/id/95/300/200', 0, 0, 0, 'approved'),
('Polly', 'Rabbit', 'Rex', 10, 'Blue', 'Male', 55, 'https://picsum.photos/id/45/300/200', 0, 0, 0, 'approved'),
('Oreo', 'Cat', 'Maine Coon', 3, 'Spotted', 'Female', 55, 'https://picsum.photos/id/75/300/200', 1, 1, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(56, 'Juan Villanueva', 41, '09484816031', '847 Niyugan, Pila, Laguna', 'juan.villanueva55@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Thumper', 'Fish', 'Goldfish', 3, 'Black', 'Female', 56, 'https://picsum.photos/id/64/300/200', 1, 0, 0, 'approved'),
('Duke', 'Dog', 'Dachshund', 12, 'Yellow', 'Female', 56, 'https://picsum.photos/id/21/300/200', 1, 0, 0, 'approved'),
('Prince', 'Bird', 'Cockatiel', 11, 'Tabby', 'Male', 56, 'https://picsum.photos/id/13/300/200', 0, 0, 0, 'approved'),
('Queen', 'Bird', 'Canary', 3, 'Spotted', 'Female', 56, 'https://picsum.photos/id/27/300/200', 1, 0, 0, 'approved'),
('Shadow', 'Fish', 'Goldfish', 3, 'Brown', 'Female', 56, 'https://picsum.photos/id/6/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(57, 'Veronica Martinez', 48, '09572663398', '28 Mojon, Pila, Laguna', 'veronica.martinez56@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Shadow', 'Bird', 'Finch', 12, 'Tabby', 'Female', 57, 'https://picsum.photos/id/45/300/200', 1, 0, 0, 'approved'),
('Happy', 'Fish', 'Angelfish', 10, 'Brindle', 'Male', 57, 'https://picsum.photos/id/61/300/200', 1, 0, 0, 'approved'),
('Prince', 'Fish', 'Betta', 11, 'Cream', 'Male', 57, 'https://picsum.photos/id/62/300/200', 0, 0, 0, 'approved'),
('Polly', 'Rabbit', 'Flemish Giant', 4, 'White', 'Male', 57, 'https://picsum.photos/id/100/300/200', 1, 0, 0, 'approved'),
('Duke', 'Dog', 'Aspin', 4, 'Blue', 'Female', 57, 'https://picsum.photos/id/21/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(58, 'Juan Bautista', 33, '09364547827', '102 San Miguel, Pila, Laguna', 'juan.bautista57@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Charlie', 'Rabbit', 'Rex', 4, 'Blue', 'Male', 58, 'https://picsum.photos/id/5/300/200', 0, 0, 0, 'approved'),
('Bubbles', 'Dog', 'Labrador Retriever', 9, 'Spotted', 'Female', 58, 'https://picsum.photos/id/38/300/200', 0, 0, 0, 'approved'),
('Milo', 'Bird', 'Parakeet', 9, 'Yellow', 'Male', 58, 'https://picsum.photos/id/53/300/200', 1, 0, 0, 'approved'),
('Rex', 'Rabbit', 'Holland Lop', 7, 'Brown', 'Female', 58, 'https://picsum.photos/id/65/300/200', 0, 0, 0, 'approved'),
('Rex', 'Cat', 'Sphynx', 1, 'Golden', 'Male', 58, 'https://picsum.photos/id/91/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(59, 'Daniela Cruz', 43, '09551064261', '878 Poblacion, Pila, Laguna', 'daniela.cruz58@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Nibbles', 'Fish', 'Betta', 12, 'Rainbow', 'Female', 59, 'https://picsum.photos/id/63/300/200', 0, 0, 0, 'approved'),
('Hopper', 'Cat', 'Persian', 9, 'Blue', 'Female', 59, 'https://picsum.photos/id/27/300/200', 0, 0, 0, 'approved'),
('Lucky', 'Rabbit', 'Flemish Giant', 2, 'Yellow', 'Female', 59, 'https://picsum.photos/id/25/300/200', 0, 1, 0, 'approved'),
('Rocky', 'Rabbit', 'Flemish Giant', 2, 'Red', 'Female', 59, 'https://picsum.photos/id/61/300/200', 1, 1, 0, 'approved'),
('Luna', 'Rabbit', 'Mini Lop', 9, 'Golden', 'Male', 59, 'https://picsum.photos/id/86/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(60, 'Diego Santos', 37, '09744167450', '157 San Antonio, Pila, Laguna', 'diego.santos59@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Leo', 'Fish', 'Discus', 4, 'Gray', 'Female', 60, 'https://picsum.photos/id/16/300/200', 0, 0, 0, 'approved'),
('Coco', 'Bird', 'African Grey', 9, 'Tabby', 'Female', 60, 'https://picsum.photos/id/69/300/200', 0, 0, 0, 'approved'),
('Lucky', 'Dog', 'Shih Tzu', 11, 'Golden', 'Male', 60, 'https://picsum.photos/id/78/300/200', 1, 0, 0, 'approved'),
('Duke', 'Fish', 'Goldfish', 3, 'Blue', 'Male', 60, 'https://picsum.photos/id/62/300/200', 1, 0, 0, 'approved'),
('Max', 'Rabbit', 'Flemish Giant', 9, 'Brindle', 'Female', 60, 'https://picsum.photos/id/52/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(61, 'Andres Domingo', 41, '09567703841', '769 Niyugan, Pila, Laguna', 'andres.domingo60@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Nibbles', 'Dog', 'German Shepherd', 8, 'Black', 'Female', 61, 'https://picsum.photos/id/56/300/200', 0, 0, 0, 'approved'),
('Daisy', 'Dog', 'Beagle', 8, 'Golden', 'Male', 61, 'https://picsum.photos/id/61/300/200', 1, 0, 0, 'approved'),
('Bean', 'Rabbit', 'Holland Lop', 7, 'Golden', 'Male', 61, 'https://picsum.photos/id/51/300/200', 0, 0, 0, 'approved'),
('Bubbles', 'Rabbit', 'Netherland Dwarf', 8, 'Rainbow', 'Female', 61, 'https://picsum.photos/id/79/300/200', 1, 0, 0, 'approved'),
('Queen', 'Rabbit', 'Rex', 8, 'Blue', 'Female', 61, 'https://picsum.photos/id/43/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(62, 'Pedro Ramirez', 55, '09449001099', '653 Bonifacio Ave., Pila, Laguna', 'pedro.ramirez61@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Thumper', 'Cat', 'Bengal', 6, 'Gray', 'Female', 62, 'https://picsum.photos/id/43/300/200', 1, 0, 0, 'approved'),
('Nibbles', 'Fish', 'Koi', 11, 'Brown', 'Female', 62, 'https://picsum.photos/id/85/300/200', 1, 0, 0, 'approved'),
('Queen', 'Rabbit', 'Rex', 2, 'White', 'Female', 62, 'https://picsum.photos/id/82/300/200', 1, 0, 0, 'approved'),
('Rocky', 'Fish', 'Angelfish', 1, 'Spotted', 'Female', 62, 'https://picsum.photos/id/25/300/200', 1, 1, 0, 'approved'),
('Jelly', 'Dog', 'Askal', 1, 'Orange', 'Male', 62, 'https://picsum.photos/id/76/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(63, 'Juan Rivera', 34, '09314520847', '619 Linga, Pila, Laguna', 'juan.rivera62@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Rocky', 'Rabbit', 'Holland Lop', 12, 'Brown', 'Female', 63, 'https://picsum.photos/id/41/300/200', 1, 0, 0, 'approved'),
('Finny', 'Rabbit', 'Flemish Giant', 1, 'Orange', 'Female', 63, 'https://picsum.photos/id/13/300/200', 1, 0, 0, 'approved'),
('Milo', 'Dog', 'Golden Retriever', 7, 'Golden', 'Male', 63, 'https://picsum.photos/id/71/300/200', 1, 0, 0, 'approved'),
('Queen', 'Dog', 'Pomeranian', 9, 'Spotted', 'Male', 63, 'https://picsum.photos/id/85/300/200', 0, 0, 0, 'approved'),
('Luna', 'Fish', 'Guppy', 12, 'Golden', 'Male', 63, 'https://picsum.photos/id/62/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(64, 'Lucia Cruz', 20, '09234295722', '153 Magsaysay Ave., Pila, Laguna', 'lucia.cruz63@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Waffles', 'Dog', 'German Shepherd', 5, 'White', 'Female', 64, 'https://picsum.photos/id/83/300/200', 0, 0, 0, 'approved'),
('Jelly', 'Dog', 'Rottweiler', 8, 'Brindle', 'Male', 64, 'https://picsum.photos/id/68/300/200', 0, 0, 0, 'approved'),
('Cookie', 'Fish', 'Angelfish', 8, 'Yellow', 'Male', 64, 'https://picsum.photos/id/61/300/200', 0, 0, 0, 'approved'),
('Milo', 'Cat', 'Maine Coon', 9, 'Spotted', 'Male', 64, 'https://picsum.photos/id/12/300/200', 1, 0, 0, 'approved'),
('Cookie', 'Fish', 'Discus', 1, 'Golden', 'Male', 64, 'https://picsum.photos/id/82/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(65, 'Valentina Francisco', 45, '09644019355', '906 San Roque, Pila, Laguna', 'valentina.francisco64@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Muffin', 'Cat', 'Siamese', 1, 'Black', 'Male', 65, 'https://picsum.photos/id/76/300/200', 0, 0, 0, 'approved'),
('Mittens', 'Rabbit', 'Holland Lop', 7, 'Calico', 'Female', 65, 'https://picsum.photos/id/40/300/200', 0, 0, 0, 'approved'),
('Brownie', 'Cat', 'Puspin', 2, 'Orange', 'Female', 65, 'https://picsum.photos/id/77/300/200', 1, 0, 0, 'approved'),
('Brownie', 'Fish', 'Discus', 8, 'Golden', 'Female', 65, 'https://picsum.photos/id/26/300/200', 0, 0, 0, 'approved'),
('Polly', 'Rabbit', 'Netherland Dwarf', 1, 'Cream', 'Male', 65, 'https://picsum.photos/id/5/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(66, 'Carmen Flores', 42, '09849499860', '211 Luna St., Pila, Laguna', 'carmen.flores65@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Pickles', 'Rabbit', 'Flemish Giant', 4, 'Brown', 'Male', 66, 'https://picsum.photos/id/56/300/200', 1, 0, 0, 'approved'),
('Princess', 'Cat', 'Ragdoll', 4, 'Orange', 'Female', 66, 'https://picsum.photos/id/66/300/200', 0, 0, 0, 'approved'),
('Luna', 'Rabbit', 'Rex', 1, 'Yellow', 'Male', 66, 'https://picsum.photos/id/21/300/200', 1, 0, 0, 'approved'),
('Prince', 'Rabbit', 'Rex', 11, 'White', 'Female', 66, 'https://picsum.photos/id/27/300/200', 0, 0, 0, 'approved'),
('Brownie', 'Dog', 'Bulldog', 10, 'Red', 'Male', 66, 'https://picsum.photos/id/37/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(67, 'Andres Rodriguez', 47, '09596957348', '31 Linga, Pila, Laguna', 'andres.rodriguez66@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Whiskers', 'Rabbit', 'Flemish Giant', 1, 'Yellow', 'Male', 67, 'https://picsum.photos/id/40/300/200', 1, 0, 0, 'approved'),
('Whiskers', 'Bird', 'Finch', 4, 'Rainbow', 'Female', 67, 'https://picsum.photos/id/55/300/200', 0, 0, 0, 'approved'),
('Pudding', 'Dog', 'German Shepherd', 4, 'Spotted', 'Female', 67, 'https://picsum.photos/id/45/300/200', 1, 0, 0, 'approved'),
('Pancake', 'Cat', 'British Shorthair', 12, 'Red', 'Male', 67, 'https://picsum.photos/id/91/300/200', 0, 0, 0, 'approved'),
('Marshmallow', 'Cat', 'Scottish Fold', 6, 'Tabby', 'Female', 67, 'https://picsum.photos/id/38/300/200', 0, 1, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(68, 'Camila Perez', 25, '09529610642', '649 Pinagbayanan, Pila, Laguna', 'camila.perez67@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Daisy', 'Cat', 'Sphynx', 2, 'Gray', 'Male', 68, 'https://picsum.photos/id/86/300/200', 1, 0, 0, 'approved'),
('Thumper', 'Rabbit', 'Flemish Giant', 11, 'White', 'Female', 68, 'https://picsum.photos/id/21/300/200', 0, 0, 0, 'approved'),
('Queen', 'Rabbit', 'Holland Lop', 5, 'Blue', 'Male', 68, 'https://picsum.photos/id/26/300/200', 0, 0, 0, 'approved'),
('Coco', 'Fish', 'Betta', 10, 'Gray', 'Female', 68, 'https://picsum.photos/id/72/300/200', 0, 0, 0, 'approved'),
('Princess', 'Bird', 'Macaw', 7, 'Brindle', 'Female', 68, 'https://picsum.photos/id/56/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(69, 'Claudia Gonzales', 21, '09523751881', '241 Bulilan, Pila, Laguna', 'claudia.gonzales68@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Happy', 'Fish', 'Angelfish', 4, 'Calico', 'Female', 69, 'https://picsum.photos/id/40/300/200', 1, 0, 0, 'approved'),
('Goldie', 'Cat', 'British Shorthair', 4, 'Yellow', 'Male', 69, 'https://picsum.photos/id/61/300/200', 1, 0, 0, 'approved'),
('Coco', 'Bird', 'African Grey', 2, 'Spotted', 'Male', 69, 'https://picsum.photos/id/2/300/200', 0, 0, 0, 'approved'),
('Luna', 'Fish', 'Angelfish', 9, 'Black', 'Male', 69, 'https://picsum.photos/id/30/300/200', 1, 0, 0, 'approved'),
('Fluffy', 'Cat', 'Bengal', 10, 'Brindle', 'Male', 69, 'https://picsum.photos/id/61/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(70, 'Isabella Lopez', 39, '09397536217', '143 Burgos St., Pila, Laguna', 'isabella.lopez69@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Prince', 'Rabbit', 'Mini Lop', 4, 'Black', 'Female', 70, 'https://picsum.photos/id/28/300/200', 0, 0, 0, 'approved'),
('Max', 'Cat', 'Scottish Fold', 3, 'Spotted', 'Female', 70, 'https://picsum.photos/id/95/300/200', 1, 0, 0, 'approved'),
('Finny', 'Dog', 'Golden Retriever', 12, 'Gray', 'Male', 70, 'https://picsum.photos/id/3/300/200', 0, 0, 0, 'approved'),
('Hopper', 'Dog', 'Rottweiler', 7, 'Yellow', 'Male', 70, 'https://picsum.photos/id/49/300/200', 1, 0, 0, 'approved'),
('Pickles', 'Fish', 'Koi', 9, 'Calico', 'Female', 70, 'https://picsum.photos/id/19/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(71, 'Isabella Santos', 36, '09199413599', '321 Rizal St., Pila, Laguna', 'isabella.santos70@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Tweety', 'Bird', 'Parakeet', 7, 'Orange', 'Female', 71, 'https://picsum.photos/id/80/300/200', 0, 0, 0, 'approved'),
('Milo', 'Cat', 'Bengal', 9, 'Black', 'Male', 71, 'https://picsum.photos/id/47/300/200', 0, 0, 0, 'approved'),
('Tweety', 'Dog', 'Pomeranian', 10, 'Cream', 'Male', 71, 'https://picsum.photos/id/83/300/200', 0, 0, 0, 'approved'),
('Simba', 'Bird', 'African Grey', 3, 'Black', 'Female', 71, 'https://picsum.photos/id/7/300/200', 0, 1, 0, 'approved'),
('Jelly', 'Fish', 'Goldfish', 8, 'Red', 'Female', 71, 'https://picsum.photos/id/59/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(72, 'Daniela Navarro', 34, '09191015868', '614 Mojon, Pila, Laguna', 'daniela.navarro71@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Finny', 'Rabbit', 'Flemish Giant', 2, 'Tabby', 'Male', 72, 'https://picsum.photos/id/82/300/200', 0, 0, 0, 'approved'),
('Max', 'Fish', 'Angelfish', 10, 'Cream', 'Male', 72, 'https://picsum.photos/id/85/300/200', 0, 0, 0, 'approved'),
('Prince', 'Cat', 'Persian', 6, 'Yellow', 'Male', 72, 'https://picsum.photos/id/68/300/200', 1, 0, 0, 'approved'),
('Polly', 'Rabbit', 'Holland Lop', 6, 'Red', 'Male', 72, 'https://picsum.photos/id/59/300/200', 1, 0, 0, 'approved'),
('Finny', 'Fish', 'Angelfish', 9, 'Gray', 'Male', 72, 'https://picsum.photos/id/48/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(73, 'Ricardo Aquino', 54, '09343258914', '811 Tulay, Pila, Laguna', 'ricardo.aquino72@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Finny', 'Rabbit', 'Flemish Giant', 7, 'White', 'Male', 73, 'https://picsum.photos/id/8/300/200', 0, 0, 0, 'approved'),
('Cupcake', 'Cat', 'Bengal', 9, 'Spotted', 'Female', 73, 'https://picsum.photos/id/96/300/200', 1, 0, 0, 'approved'),
('Cupcake', 'Cat', 'Persian', 5, 'Golden', 'Male', 73, 'https://picsum.photos/id/82/300/200', 0, 0, 0, 'approved'),
('Brownie', 'Bird', 'African Grey', 3, 'Brindle', 'Male', 73, 'https://picsum.photos/id/74/300/200', 0, 0, 0, 'approved'),
('Pickles', 'Fish', 'Guppy', 5, 'Golden', 'Male', 73, 'https://picsum.photos/id/30/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(74, 'Claudia Cruz', 45, '09451658654', '56 Aguinaldo St., Pila, Laguna', 'claudia.cruz73@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Sparky', 'Fish', 'Angelfish', 5, 'Calico', 'Female', 74, 'https://picsum.photos/id/84/300/200', 1, 0, 0, 'approved'),
('Nala', 'Cat', 'Ragdoll', 3, 'Brown', 'Male', 74, 'https://picsum.photos/id/47/300/200', 1, 0, 0, 'approved'),
('Coco', 'Bird', 'African Grey', 5, 'Red', 'Female', 74, 'https://picsum.photos/id/16/300/200', 0, 0, 0, 'approved'),
('Duke', 'Dog', 'Aspin', 10, 'Golden', 'Male', 74, 'https://picsum.photos/id/55/300/200', 0, 0, 0, 'approved'),
('Milo', 'Fish', 'Discus', 10, 'Tabby', 'Female', 74, 'https://picsum.photos/id/66/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(75, 'Carlos Garcia', 55, '09464525432', '84 Pinagbayanan, Pila, Laguna', 'carlos.garcia74@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Coco', 'Cat', 'Persian', 8, 'Blue', 'Male', 75, 'https://picsum.photos/id/72/300/200', 0, 0, 0, 'approved'),
('Cupcake', 'Fish', 'Koi', 8, 'Yellow', 'Female', 75, 'https://picsum.photos/id/80/300/200', 1, 0, 0, 'approved'),
('Waffles', 'Dog', 'Askal', 12, 'Gray', 'Female', 75, 'https://picsum.photos/id/66/300/200', 0, 1, 0, 'approved'),
('Milo', 'Bird', 'Lovebird', 2, 'Green', 'Female', 75, 'https://picsum.photos/id/52/300/200', 1, 0, 0, 'approved'),
('Coco', 'Fish', 'Koi', 1, 'Yellow', 'Female', 75, 'https://picsum.photos/id/24/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(76, 'Ana Gonzales', 40, '09262439208', '711 San Miguel, Pila, Laguna', 'ana.gonzales75@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Pickles', 'Dog', 'Beagle', 1, 'White', 'Male', 76, 'https://picsum.photos/id/75/300/200', 1, 0, 0, 'approved'),
('Goldie', 'Dog', 'Golden Retriever', 12, 'Green', 'Male', 76, 'https://picsum.photos/id/3/300/200', 0, 0, 0, 'approved'),
('Sunny', 'Rabbit', 'Rex', 1, 'Brown', 'Female', 76, 'https://picsum.photos/id/78/300/200', 0, 0, 0, 'approved'),
('Lucky', 'Dog', 'Golden Retriever', 6, 'Green', 'Female', 76, 'https://picsum.photos/id/69/300/200', 1, 0, 0, 'approved'),
('Pickles', 'Rabbit', 'Holland Lop', 2, 'Black', 'Female', 76, 'https://picsum.photos/id/44/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(77, 'Manuel Francisco', 55, '09232988181', '703 Mojon, Pila, Laguna', 'manuel.francisco76@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Nibbles', 'Cat', 'Siamese', 4, 'Brindle', 'Male', 77, 'https://picsum.photos/id/50/300/200', 0, 0, 0, 'approved'),
('Charlie', 'Cat', 'Domestic Shorthair', 12, 'Tabby', 'Female', 77, 'https://picsum.photos/id/5/300/200', 1, 0, 0, 'approved'),
('Rocky', 'Bird', 'Canary', 9, 'Cream', 'Male', 77, 'https://picsum.photos/id/76/300/200', 1, 0, 0, 'approved'),
('Simba', 'Cat', 'Ragdoll', 10, 'Green', 'Male', 77, 'https://picsum.photos/id/34/300/200', 1, 0, 0, 'approved'),
('Queen', 'Bird', 'Macaw', 10, 'Spotted', 'Male', 77, 'https://picsum.photos/id/34/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(78, 'Daniela Martinez', 41, '09860944877', '313 Bulilan, Pila, Laguna', 'daniela.martinez77@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Happy', 'Fish', 'Koi', 7, 'Spotted', 'Male', 78, 'https://picsum.photos/id/70/300/200', 0, 0, 0, 'approved'),
('Finny', 'Cat', 'Bengal', 6, 'Rainbow', 'Female', 78, 'https://picsum.photos/id/13/300/200', 1, 0, 0, 'approved'),
('Thumper', 'Dog', 'Rottweiler', 9, 'Green', 'Female', 78, 'https://picsum.photos/id/100/300/200', 1, 1, 0, 'approved'),
('Rex', 'Fish', 'Koi', 2, 'White', 'Female', 78, 'https://picsum.photos/id/26/300/200', 1, 1, 0, 'approved'),
('Cookie', 'Dog', 'Aspin', 5, 'Brown', 'Female', 78, 'https://picsum.photos/id/55/300/200', 1, 1, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(79, 'Carlos Reyes', 26, '09811945149', '301 Magsaysay Ave., Pila, Laguna', 'carlos.reyes78@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('King', 'Cat', 'Domestic Shorthair', 1, 'Black', 'Female', 79, 'https://picsum.photos/id/18/300/200', 1, 0, 0, 'approved'),
('Lucky', 'Dog', 'Beagle', 5, 'Black', 'Male', 79, 'https://picsum.photos/id/85/300/200', 1, 0, 0, 'approved'),
('Thumper', 'Bird', 'Macaw', 6, 'Brindle', 'Male', 79, 'https://picsum.photos/id/100/300/200', 1, 0, 0, 'approved'),
('Bella', 'Rabbit', 'Rex', 2, 'White', 'Male', 79, 'https://picsum.photos/id/100/300/200', 0, 0, 0, 'approved'),
('Daisy', 'Fish', 'Koi', 8, 'Orange', 'Female', 79, 'https://picsum.photos/id/88/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(80, 'Lucia Aquino', 20, '09458768153', '972 Burgos St., Pila, Laguna', 'lucia.aquino79@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Lucy', 'Cat', 'Domestic Shorthair', 6, 'Black', 'Male', 80, 'https://picsum.photos/id/21/300/200', 1, 0, 0, 'approved'),
('Max', 'Dog', 'Beagle', 4, 'Black', 'Female', 80, 'https://picsum.photos/id/88/300/200', 1, 0, 0, 'approved'),
('Pickles', 'Dog', 'Shih Tzu', 2, 'Gray', 'Male', 80, 'https://picsum.photos/id/53/300/200', 1, 0, 0, 'approved'),
('Sunny', 'Dog', 'Dachshund', 11, 'Golden', 'Female', 80, 'https://picsum.photos/id/77/300/200', 0, 0, 0, 'approved'),
('Rocky', 'Dog', 'Dachshund', 8, 'Green', 'Male', 80, 'https://picsum.photos/id/86/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(81, 'Alejandro Rodriguez', 20, '09354183224', '693 San Antonio, Pila, Laguna', 'alejandro.rodriguez80@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Nibbles', 'Dog', 'Pomeranian', 2, 'Brown', 'Male', 81, 'https://picsum.photos/id/70/300/200', 0, 0, 0, 'approved'),
('Leo', 'Bird', 'Lovebird', 4, 'Brown', 'Female', 81, 'https://picsum.photos/id/41/300/200', 0, 0, 0, 'approved'),
('Pancake', 'Cat', 'Bengal', 7, 'Golden', 'Female', 81, 'https://picsum.photos/id/17/300/200', 0, 0, 0, 'approved'),
('Muffin', 'Cat', 'Persian', 10, 'Spotted', 'Male', 81, 'https://picsum.photos/id/23/300/200', 0, 0, 0, 'approved'),
('Thumper', 'Dog', 'Labrador Retriever', 8, 'Red', 'Male', 81, 'https://picsum.photos/id/67/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(82, 'Isabella Castro', 25, '09337721779', '959 Burgos St., Pila, Laguna', 'isabella.castro81@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Angel', 'Fish', 'Goldfish', 2, 'Spotted', 'Male', 82, 'https://picsum.photos/id/82/300/200', 0, 0, 0, 'approved'),
('Peanut', 'Fish', 'Betta', 6, 'Tabby', 'Female', 82, 'https://picsum.photos/id/60/300/200', 0, 0, 0, 'approved'),
('Milo', 'Fish', 'Angelfish', 6, 'Cream', 'Female', 82, 'https://picsum.photos/id/10/300/200', 1, 0, 0, 'approved'),
('Mochi', 'Rabbit', 'Mini Lop', 12, 'Gray', 'Female', 82, 'https://picsum.photos/id/17/300/200', 0, 0, 0, 'approved'),
('Daisy', 'Dog', 'Golden Retriever', 1, 'Calico', 'Male', 82, 'https://picsum.photos/id/39/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(83, 'Maria Francisco', 55, '09201845040', '897 Bonifacio Ave., Pila, Laguna', 'maria.francisco82@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Shadow', 'Dog', 'Aspin', 5, 'Rainbow', 'Female', 83, 'https://picsum.photos/id/18/300/200', 1, 0, 0, 'approved'),
('Charlie', 'Cat', 'Persian', 6, 'Orange', 'Female', 83, 'https://picsum.photos/id/95/300/200', 1, 1, 0, 'approved'),
('Waffles', 'Fish', 'Goldfish', 4, 'White', 'Female', 83, 'https://picsum.photos/id/81/300/200', 0, 0, 0, 'approved'),
('Waffles', 'Fish', 'Angelfish', 6, 'Green', 'Male', 83, 'https://picsum.photos/id/13/300/200', 1, 0, 0, 'approved'),
('Pickles', 'Fish', 'Betta', 6, 'Tabby', 'Male', 83, 'https://picsum.photos/id/37/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(84, 'Maria Ramos', 55, '09343604535', '196 San Antonio, Pila, Laguna', 'maria.ramos83@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Rex', 'Fish', 'Discus', 9, 'Brown', 'Male', 84, 'https://picsum.photos/id/63/300/200', 0, 0, 0, 'approved'),
('Buddy', 'Fish', 'Betta', 7, 'White', 'Female', 84, 'https://picsum.photos/id/17/300/200', 0, 0, 0, 'approved'),
('Lucy', 'Dog', 'German Shepherd', 4, 'Green', 'Female', 84, 'https://picsum.photos/id/79/300/200', 1, 0, 0, 'approved'),
('Brownie', 'Dog', 'Pomeranian', 1, 'Brindle', 'Male', 84, 'https://picsum.photos/id/42/300/200', 1, 0, 0, 'approved'),
('Hopper', 'Rabbit', 'Netherland Dwarf', 8, 'Red', 'Female', 84, 'https://picsum.photos/id/85/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(85, 'Ricardo Hernandez', 36, '09275699556', '201 Bonifacio Ave., Pila, Laguna', 'ricardo.hernandez84@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Angel', 'Dog', 'Aspin', 11, 'Spotted', 'Female', 85, 'https://picsum.photos/id/75/300/200', 0, 0, 0, 'approved'),
('Leo', 'Rabbit', 'Flemish Giant', 4, 'Black', 'Male', 85, 'https://picsum.photos/id/53/300/200', 1, 0, 0, 'approved'),
('Leo', 'Cat', 'Scottish Fold', 10, 'Calico', 'Male', 85, 'https://picsum.photos/id/58/300/200', 1, 0, 0, 'approved'),
('Nala', 'Bird', 'African Grey', 12, 'Blue', 'Female', 85, 'https://picsum.photos/id/29/300/200', 1, 0, 0, 'approved'),
('Lucy', 'Rabbit', 'Flemish Giant', 6, 'White', 'Male', 85, 'https://picsum.photos/id/46/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(86, 'Patricia Villar', 62, '09906411472', '953 Magsaysay Ave., Pila, Laguna', 'patricia.villar85@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Milo', 'Fish', 'Betta', 12, 'Yellow', 'Female', 86, 'https://picsum.photos/id/87/300/200', 1, 0, 0, 'approved'),
('King', 'Rabbit', 'Mini Lop', 12, 'Orange', 'Male', 86, 'https://picsum.photos/id/80/300/200', 1, 0, 0, 'approved'),
('Mittens', 'Rabbit', 'Rex', 11, 'Rainbow', 'Male', 86, 'https://picsum.photos/id/9/300/200', 0, 0, 0, 'approved'),
('Polly', 'Rabbit', 'Mini Lop', 1, 'Calico', 'Female', 86, 'https://picsum.photos/id/4/300/200', 0, 0, 0, 'approved'),
('Nala', 'Dog', 'Beagle', 3, 'Tabby', 'Female', 86, 'https://picsum.photos/id/51/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(87, 'Ricardo Rodriguez', 20, '09168301294', '310 San Antonio, Pila, Laguna', 'ricardo.rodriguez86@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Luna', 'Dog', 'Poodle', 4, 'Yellow', 'Female', 87, 'https://picsum.photos/id/2/300/200', 1, 0, 0, 'approved'),
('Pancake', 'Rabbit', 'Netherland Dwarf', 11, 'Golden', 'Male', 87, 'https://picsum.photos/id/32/300/200', 1, 0, 0, 'approved'),
('Sparky', 'Bird', 'Parakeet', 4, 'Calico', 'Male', 87, 'https://picsum.photos/id/25/300/200', 0, 0, 0, 'approved'),
('Buddy', 'Rabbit', 'Netherland Dwarf', 10, 'Brown', 'Female', 87, 'https://picsum.photos/id/33/300/200', 0, 0, 0, 'approved'),
('Mittens', 'Dog', 'Askal', 3, 'Orange', 'Male', 87, 'https://picsum.photos/id/88/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(88, 'Fernando Torres', 58, '09328032875', '436 Luna St., Pila, Laguna', 'fernando.torres87@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Queen', 'Bird', 'Parakeet', 5, 'Cream', 'Male', 88, 'https://picsum.photos/id/95/300/200', 1, 0, 0, 'approved'),
('Tweety', 'Dog', 'Poodle', 2, 'Golden', 'Female', 88, 'https://picsum.photos/id/99/300/200', 1, 1, 0, 'approved'),
('Duke', 'Bird', 'Finch', 5, 'Blue', 'Male', 88, 'https://picsum.photos/id/22/300/200', 1, 0, 0, 'approved'),
('Duke', 'Cat', 'Domestic Shorthair', 10, 'Gray', 'Male', 88, 'https://picsum.photos/id/84/300/200', 0, 0, 0, 'approved'),
('Lucy', 'Dog', 'Dachshund', 2, 'Brindle', 'Male', 88, 'https://picsum.photos/id/29/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(89, 'Jose Domingo', 65, '09609722632', '859 San Miguel, Pila, Laguna', 'jose.domingo88@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Thumper', 'Cat', 'Puspin', 8, 'Black', 'Male', 89, 'https://picsum.photos/id/74/300/200', 0, 0, 0, 'approved'),
('Coco', 'Rabbit', 'Mini Lop', 5, 'Orange', 'Female', 89, 'https://picsum.photos/id/96/300/200', 0, 0, 0, 'approved'),
('Bubbles', 'Cat', 'Sphynx', 8, 'Green', 'Male', 89, 'https://picsum.photos/id/79/300/200', 0, 0, 0, 'approved'),
('Duke', 'Rabbit', 'Rex', 9, 'Spotted', 'Male', 89, 'https://picsum.photos/id/20/300/200', 1, 0, 0, 'approved'),
('Coco', 'Bird', 'Lovebird', 12, 'Brindle', 'Female', 89, 'https://picsum.photos/id/75/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(90, 'Pedro Ocampo', 26, '09504962864', '537 San Antonio, Pila, Laguna', 'pedro.ocampo89@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Marshmallow', 'Dog', 'Labrador Retriever', 1, 'Tabby', 'Male', 90, 'https://picsum.photos/id/40/300/200', 1, 0, 0, 'approved'),
('Duke', 'Dog', 'Labrador Retriever', 1, 'Yellow', 'Male', 90, 'https://picsum.photos/id/23/300/200', 1, 1, 0, 'approved'),
('Charlie', 'Fish', 'Discus', 12, 'Black', 'Male', 90, 'https://picsum.photos/id/95/300/200', 1, 0, 0, 'approved'),
('Max', 'Cat', 'Domestic Shorthair', 6, 'Red', 'Male', 90, 'https://picsum.photos/id/57/300/200', 0, 0, 0, 'approved'),
('Buddy', 'Bird', 'Canary', 1, 'Green', 'Female', 90, 'https://picsum.photos/id/51/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(91, 'Juan Rivera', 35, '09280371519', '176 Niyugan, Pila, Laguna', 'juan.rivera90@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Max', 'Dog', 'Pomeranian', 11, 'Gray', 'Female', 91, 'https://picsum.photos/id/90/300/200', 0, 0, 0, 'approved'),
('Angel', 'Rabbit', 'Netherland Dwarf', 12, 'Golden', 'Male', 91, 'https://picsum.photos/id/91/300/200', 1, 0, 0, 'approved'),
('Hopper', 'Cat', 'Siamese', 3, 'Green', 'Female', 91, 'https://picsum.photos/id/56/300/200', 0, 1, 0, 'approved'),
('Angel', 'Bird', 'Lovebird', 7, 'Spotted', 'Male', 91, 'https://picsum.photos/id/37/300/200', 0, 0, 0, 'approved'),
('Polly', 'Bird', 'Lovebird', 11, 'Cream', 'Female', 91, 'https://picsum.photos/id/55/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(92, 'Luis Reyes', 58, '09961022557', '693 Pansol, Pila, Laguna', 'luis.reyes91@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Angel', 'Bird', 'African Grey', 10, 'Black', 'Female', 92, 'https://picsum.photos/id/72/300/200', 0, 0, 0, 'approved'),
('Rex', 'Dog', 'Rottweiler', 9, 'Brown', 'Female', 92, 'https://picsum.photos/id/78/300/200', 0, 0, 0, 'approved'),
('Sunny', 'Cat', 'Siamese', 4, 'Golden', 'Male', 92, 'https://picsum.photos/id/65/300/200', 0, 0, 0, 'approved'),
('Hopper', 'Bird', 'African Grey', 5, 'White', 'Female', 92, 'https://picsum.photos/id/52/300/200', 0, 1, 0, 'approved'),
('Muffin', 'Bird', 'Macaw', 12, 'White', 'Female', 92, 'https://picsum.photos/id/39/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(93, 'Maria Garcia', 51, '09911734755', '395 Pansol, Pila, Laguna', 'maria.garcia92@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Brownie', 'Fish', 'Angelfish', 6, 'Yellow', 'Female', 93, 'https://picsum.photos/id/21/300/200', 1, 0, 0, 'approved'),
('Polly', 'Cat', 'Maine Coon', 5, 'Orange', 'Female', 93, 'https://picsum.photos/id/10/300/200', 0, 0, 0, 'approved'),
('Happy', 'Rabbit', 'Rex', 3, 'Red', 'Female', 93, 'https://picsum.photos/id/24/300/200', 0, 0, 0, 'approved'),
('Cupcake', 'Bird', 'Lovebird', 11, 'Tabby', 'Male', 93, 'https://picsum.photos/id/46/300/200', 1, 0, 0, 'approved'),
('Peanut', 'Bird', 'Parakeet', 6, 'Black', 'Male', 93, 'https://picsum.photos/id/10/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(94, 'Raul Diaz', 19, '09648743915', '108 Mabini St., Pila, Laguna', 'raul.diaz93@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Goldie', 'Fish', 'Betta', 3, 'Orange', 'Male', 94, 'https://picsum.photos/id/36/300/200', 1, 0, 0, 'approved'),
('Rocky', 'Bird', 'African Grey', 2, 'Blue', 'Male', 94, 'https://picsum.photos/id/6/300/200', 1, 0, 0, 'approved'),
('Polly', 'Dog', 'Pomeranian', 3, 'Rainbow', 'Male', 94, 'https://picsum.photos/id/25/300/200', 0, 0, 0, 'approved'),
('Jelly', 'Fish', 'Discus', 1, 'White', 'Female', 94, 'https://picsum.photos/id/38/300/200', 0, 0, 0, 'approved'),
('Polly', 'Fish', 'Guppy', 8, 'Orange', 'Male', 94, 'https://picsum.photos/id/45/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(95, 'Maria Bautista', 19, '09783517452', '685 Linga, Pila, Laguna', 'maria.bautista94@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Leo', 'Fish', 'Discus', 10, 'Yellow', 'Male', 95, 'https://picsum.photos/id/69/300/200', 1, 0, 0, 'approved'),
('Duke', 'Fish', 'Angelfish', 2, 'Spotted', 'Female', 95, 'https://picsum.photos/id/64/300/200', 0, 0, 0, 'approved'),
('Jelly', 'Bird', 'Macaw', 5, 'Orange', 'Male', 95, 'https://picsum.photos/id/94/300/200', 0, 0, 0, 'approved'),
('Pancake', 'Rabbit', 'Netherland Dwarf', 4, 'Brown', 'Female', 95, 'https://picsum.photos/id/37/300/200', 0, 0, 0, 'approved'),
('Max', 'Rabbit', 'Mini Lop', 10, 'Green', 'Male', 95, 'https://picsum.photos/id/42/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(96, 'Valentina Francisco', 53, '09581739652', '777 Aguinaldo St., Pila, Laguna', 'valentina.francisco95@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Max', 'Dog', 'Pomeranian', 6, 'Golden', 'Male', 96, 'https://picsum.photos/id/95/300/200', 1, 0, 0, 'approved'),
('Lucy', 'Fish', 'Discus', 1, 'Cream', 'Female', 96, 'https://picsum.photos/id/88/300/200', 0, 1, 0, 'approved'),
('Charlie', 'Bird', 'African Grey', 2, 'Calico', 'Male', 96, 'https://picsum.photos/id/14/300/200', 1, 1, 0, 'approved'),
('Milo', 'Dog', 'Shih Tzu', 2, 'Calico', 'Male', 96, 'https://picsum.photos/id/80/300/200', 0, 0, 0, 'approved'),
('Pudding', 'Rabbit', 'Netherland Dwarf', 8, 'Brown', 'Female', 96, 'https://picsum.photos/id/39/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(97, 'Fernando Ramos', 38, '09496182700', '625 Pansol, Pila, Laguna', 'fernando.ramos96@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Angel', 'Bird', 'Macaw', 10, 'Gray', 'Female', 97, 'https://picsum.photos/id/57/300/200', 0, 0, 0, 'approved'),
('Peanut', 'Rabbit', 'Netherland Dwarf', 3, 'Yellow', 'Female', 97, 'https://picsum.photos/id/26/300/200', 1, 0, 0, 'approved'),
('Jelly', 'Cat', 'Ragdoll', 10, 'Red', 'Female', 97, 'https://picsum.photos/id/46/300/200', 0, 0, 0, 'approved'),
('Coco', 'Bird', 'Finch', 11, 'Golden', 'Female', 97, 'https://picsum.photos/id/6/300/200', 0, 0, 0, 'approved'),
('Daisy', 'Fish', 'Goldfish', 4, 'Tabby', 'Male', 97, 'https://picsum.photos/id/23/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(98, 'Carmen Villar', 19, '09212106732', '981 Aplaya, Pila, Laguna', 'carmen.villar97@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Thumper', 'Rabbit', 'Mini Lop', 8, 'Cream', 'Female', 98, 'https://picsum.photos/id/5/300/200', 1, 1, 0, 'approved'),
('Max', 'Fish', 'Discus', 5, 'Green', 'Male', 98, 'https://picsum.photos/id/11/300/200', 0, 0, 0, 'approved'),
('Charlie', 'Bird', 'Lovebird', 12, 'Spotted', 'Female', 98, 'https://picsum.photos/id/28/300/200', 1, 0, 0, 'approved'),
('Pancake', 'Cat', 'Sphynx', 5, 'Green', 'Female', 98, 'https://picsum.photos/id/10/300/200', 1, 1, 0, 'approved'),
('Cookie', 'Rabbit', 'Flemish Giant', 11, 'Spotted', 'Female', 98, 'https://picsum.photos/id/7/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(99, 'Fernando Morales', 57, '09409378278', '963 Luna St., Pila, Laguna', 'fernando.morales98@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Duke', 'Fish', 'Angelfish', 2, 'Cream', 'Female', 99, 'https://picsum.photos/id/4/300/200', 0, 1, 0, 'approved'),
('Oreo', 'Rabbit', 'Netherland Dwarf', 5, 'Orange', 'Female', 99, 'https://picsum.photos/id/98/300/200', 0, 0, 0, 'approved'),
('Buddy', 'Dog', 'Beagle', 5, 'Spotted', 'Female', 99, 'https://picsum.photos/id/68/300/200', 0, 0, 0, 'approved'),
('Queen', 'Fish', 'Koi', 8, 'Cream', 'Male', 99, 'https://picsum.photos/id/72/300/200', 1, 0, 0, 'approved'),
('Duke', 'Bird', 'Cockatiel', 2, 'Cream', 'Male', 99, 'https://picsum.photos/id/6/300/200', 1, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(100, 'Raul Garcia', 55, '09197420485', '729 Tulay, Pila, Laguna', 'raul.garcia99@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Lucky', 'Bird', 'Finch', 1, 'Spotted', 'Male', 100, 'https://picsum.photos/id/91/300/200', 1, 0, 0, 'approved'),
('Hopper', 'Bird', 'Finch', 7, 'Yellow', 'Female', 100, 'https://picsum.photos/id/94/300/200', 0, 0, 0, 'approved'),
('Daisy', 'Dog', 'Bulldog', 1, 'White', 'Male', 100, 'https://picsum.photos/id/65/300/200', 0, 0, 0, 'approved'),
('Waffles', 'Dog', 'Dachshund', 1, 'Golden', 'Female', 100, 'https://picsum.photos/id/52/300/200', 1, 0, 0, 'approved'),
('Prince', 'Fish', 'Guppy', 4, 'Black', 'Female', 100, 'https://picsum.photos/id/82/300/200', 0, 0, 0, 'approved');

INSERT INTO users (id, full_name, age, contact_number, address, email, password, is_admin) VALUES
(101, 'Alejandro Villar', 50, '09366210047', '562 Burgos St., Pila, Laguna', 'alejandro.villar100@example.com', '$2y$12$OUw4ZmY9F.halEm6RJPaouj7dLoGt6t8pQGsMmrgAKlEc033d3gcG', 0);

INSERT INTO pets (name, category, pet_type, age, color, gender, owner_id, photo_url, available_for_adoption, lost, deceased, status) VALUES
('Lucky', 'Fish', 'Koi', 9, 'Rainbow', 'Male', 101, 'https://picsum.photos/id/94/300/200', 1, 0, 0, 'approved'),
('Bean', 'Fish', 'Guppy', 12, 'Calico', 'Male', 101, 'https://picsum.photos/id/79/300/200', 1, 0, 0, 'approved'),
('Rex', 'Rabbit', 'Rex', 1, 'Spotted', 'Female', 101, 'https://picsum.photos/id/20/300/200', 0, 0, 0, 'approved'),
('Pudding', 'Rabbit', 'Flemish Giant', 12, 'Gray', 'Male', 101, 'https://picsum.photos/id/62/300/200', 1, 0, 0, 'approved'),
('Whiskers', 'Dog', 'Golden Retriever', 9, 'Rainbow', 'Male', 101, 'https://picsum.photos/id/68/300/200', 1, 0, 0, 'approved');

SET FOREIGN_KEY_CHECKS=1;
-- Done. 100 users + 500 pets inserted.
