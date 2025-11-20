-- Migration: add 'cantidad' column to experiencias
-- Run on your MySQL database for the project

ALTER TABLE `experiencias`
  ADD COLUMN `cantidad` INT NOT NULL DEFAULT 1 AFTER `duracion`;
