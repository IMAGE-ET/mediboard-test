INSERT INTO `forum_theme` (`forum_theme_id`, `title`, `desc`) VALUES 
(1, 'Theme 1', 'Voici un nouveau theme qu''il est beau');

INSERT INTO `forum_thread` (`forum_thread_id`, `forum_theme_id`, `title`, `desc`, `user_id`) VALUES 
(1, 1, 'Premier thread', 'Un truc qui sert à pas grand chose', 158);

INSERT INTO `forum_message` (`forum_message_id`, `forum_thread_id`, `title`, `body`, `user_id`) VALUES 
(1, 1, '', 'premier message du premier thread, contenu du message', 158);
