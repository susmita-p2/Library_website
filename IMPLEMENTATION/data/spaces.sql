INSERT INTO spaces (space_id,space_name, space_capacity)
VALUES (1,'room101', 50),
       (2,'room102', 30),
       (3,'room201', 20),
       (4,'room301', 40),
       (5,'discussion room',10);

INSERT INTO space_reservations(space_id,user_id,space_res_date, space_res_start_time,space_res_end_time)
VALUES (1,'000001','2024-11-13','2024-11-13 13:34:55','2024-11-13 15:24:24'),
       (2,'000003','2024-11-14','2024-11-14 9:23:55','2024-11-14 11:36:05'),
       (3,'000002','2024-12-15','2024-11-15 14:14:14','2024-12-15 15:15:15');
