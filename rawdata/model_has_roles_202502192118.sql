INSERT INTO model_has_roles (role_id,model_type,model_id) VALUES
	 ((SELECT id FROM roles WHERE id = 26),N'App\Models\User',1),
	 ((SELECT id FROM roles WHERE id = 33),N'App\Models\User',14),
	 ((SELECT id FROM roles WHERE id = 32),N'App\Models\User',15),
	 ((SELECT id FROM roles WHERE id = 32),N'App\Models\User',16),
	 ((SELECT id FROM roles WHERE id = 32),N'App\Models\User',17),
	 ((SELECT id FROM roles WHERE id = 32),N'App\Models\User',23),
	 ((SELECT id FROM roles WHERE id = 32),N'App\Models\User',24),
	 ((SELECT id FROM roles WHERE id = 32),N'App\Models\User',25),
	 ((SELECT id FROM roles WHERE id = 32),N'App\Models\User',26);
