require 'ruble'

template "PHP Template" do |t|
  t.filetype = "*.php"
  t.location = "templates/template.php"
end

template "Mediboard Class Template" do |t|
  t.filetype = "*.php"
  t.location = "templates/class_mediboard.php"
end
