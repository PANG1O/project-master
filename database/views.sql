CREATE VIEW IF NOT EXISTS projects_view AS
SELECT projects.id,
       projects.number,
       projects.name,
       projects.description,
       projects.user_id,
       projects.status_id,
       projects.customer_id,
       projects.active,
       projects.deleted,
       projects.created,
       projects.updated,
       users.salutation,
       users.title,
       users.first_name,
       users.last_name,
       status.name AS status,
       customers.number AS customer_number,
       customers.name AS customer
FROM projects
         INNER JOIN users ON projects.user_id = users.id
         INNER JOIN status ON projects.status_id = status.id
         INNER JOIN customers ON projects.customer_id = customers.id;

CREATE VIEW IF NOT EXISTS tasks_view AS
SELECT tasks.id,
       tasks.number,
       tasks.name,
       tasks.description,
       tasks.user_id,
       tasks.status_id,
       tasks.project_id,
       tasks.deleted,
       tasks.created,
       tasks.updated,
       users.salutation,
       users.title,
       users.first_name,
       users.last_name,
       status.name AS status,
       projects.number AS project_number,
       projects.name AS project
FROM tasks
         INNER JOIN users ON tasks.user_id = users.id
         INNER JOIN status ON tasks.status_id = status.id
         INNER JOIN projects ON tasks.project_id = projects.id;
