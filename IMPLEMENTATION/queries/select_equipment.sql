SELECT equipment_id as 'id', equipment_type as 'Type', equipment_qty as "Quantity", space_id as 'Space id', space_name as 'Space'
  FROM equipment
       INNER JOIN spaces
       USING (space_id);