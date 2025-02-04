
--7days for a material

INSERT INTO material_reservations(mat_id,user_id,mat_res_start_time,mat_res_end_time)
VALUES (2,'000001','2024-11-13','2024-11-20'),
       (5,'000003','2024-11-14','2024-11-21'),
       (3,'000002','2024-12-15','2024-12-22');

INSERT INTO equipment_reservations(equipment_id,user_id,equipment_res_start_time,equipment_res_end_time)
VALUES (2,'000001','2024-12-13','2024-12-20'),
       (5,'000003','2024-12-11','2024-12-18'),
       (3,'000002','2024-11-11','2024-11-18');