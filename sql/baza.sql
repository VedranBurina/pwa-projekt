CREATE DATABASE IF NOT EXISTS frankfurter_portal
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE frankfurter_portal;

DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) DEFAULT NULL,
    summary TEXT NOT NULL,
    content TEXT NOT NULL,
    category ENUM('politika', 'sport') NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    published_at DATETIME NOT NULL,
    rating TINYINT NOT NULL DEFAULT 1,
    show_on_homepage TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO articles (title, subtitle, summary, content, category, image_path, published_at, rating, show_on_homepage) VALUES
('Premijer najavio novi paket mjera za digitalizaciju javne uprave', 'Jutarnji briefing', 'Vlada tvrdi da će novi plan ubrzati obradu zahtjeva građana i smanjiti broj odlazaka na šaltere tijekom iduće godine.', 'Predstavljeni paket uključuje objedinjeni sustav za izdavanje potvrda, jednostavniju prijavu prebivališta i praćenje statusa predmeta preko jedinstvenog korisničkog računa. Ministarstvo uprave tvrdi da su prioritet usluge koje građani najčešće koriste i one koje danas stvaraju najveće gužve.\n\nDio stručnjaka pozdravlja najavu, ali upozorava da digitalizacija neće imati puni učinak bez ulaganja u edukaciju službenika i stabilniju infrastrukturu. Oporba traži precizne rokove i javno objavljivanje troškova projekta prije početka provedbe.', 'politika', 'https://commons.wikimedia.org/wiki/Special:FilePath/Press_Conference_%2850614218148%29.jpg', '2026-06-18 09:10:00', 4, 1),
('Zastupnici raspravljaju o fondu za studentski smještaj u velikim gradovima', 'Saborska rasprava', 'Prijedlog predviđa poseban fond kojim bi se subvencionirao najam studentima u sredinama s najvišim troškovima života.', 'Tijekom rasprave istaknuto je da broj kreveta u studentskim domovima godinama ne prati rast upisanih studenata, osobito u Zagrebu i na obali. Predlagatelji zakona smatraju da bi model subvencioniranog najma bio brže i fleksibilnije rješenje od čekanja na gradnju novih kapaciteta.\n\nProtivnici mjere upozoravaju da bi država time mogla samo kratkoročno ublažiti problem, dok bi dugoročno trebalo više ulagati u domove i decentralizaciju studijskih programa. Konačno glasanje očekuje se do kraja mjeseca.', 'politika', 'https://commons.wikimedia.org/wiki/Special:FilePath/Scotland_Parliament_Holyrood.jpg', '2026-06-18 08:00:00', 5, 1),
('Guverner traži hitan dogovor oko kriznog proračuna nakon pada prihoda', 'Izvanredna izjava', 'Nakon slabijih fiskalnih rezultata vlast pokušava uskladiti rezove i nova ulaganja bez odgađanja ključnih projekata.', 'Na izvanrednoj konferenciji za medije poručeno je da će prioritet imati zdravstvo, škole i infrastrukturni radovi koji su već ugovoreni. Istodobno se razmatra odgoda dijela promotivnih kampanja i preraspodjela sredstava između ministarstava.\n\nAnalitičari procjenjuju da će ishod pregovora ovisiti o tome koliko će brzo doći do oporavka poreznih prihoda u drugom dijelu godine. Vlada poručuje da ne planira panične rezove, ali priznaje da će pojedini projekti biti ponovno vrednovani.', 'politika', 'https://commons.wikimedia.org/wiki/Special:FilePath/Gavin_Newsom_at_podium_before_a_press_conference_-_3.24.20.jpg', '2026-06-17 18:45:00', 3, 1),
('Rukometaši do pobjede stigli nakon serije obrana u posljednjih pet minuta', 'Napeta završnica', 'Utakmica je dugo bila izjednačena, a domaći sastav slomio je otpor suparnika tek u samoj završnici susreta.', 'Ključni trenutak dogodio se kada je vratar domaćih upisao tri uzastopne obrane i pokrenuo kontru iz koje je pala odlučujuća razlika. Trener je nakon utakmice istaknuo da je momčad prvi put nakon više kola odigrala disciplinirano i bez nepotrebnih isključenja u završnici.\n\nGostujući sastav imao je priliku vratiti se u igru sedmercem minutu prije kraja, ali je lopta završila u bloku. Publika je glasnim navijanjem ispratila momčad koja je ovom pobjedom ostala u utrci za vrh ljestvice.', 'sport', 'https://commons.wikimedia.org/wiki/Special:FilePath/Handball_golakeeper.ahcp.00.jpg', '2026-06-18 11:30:00', 5, 1),
('Veliki stadion pred ljeto dobiva novu rasvjetu i obnovu prilaznih zona', 'Plan modernizacije', 'Uprava objekta najavila je radove koji bi trebali poboljšati sigurnost, izgled i uvjete za održavanje večernjih događanja.', 'Projekt uključuje zamjenu dotrajale rasvjete energetski učinkovitijim sustavom, obnovu ulaznih koridora i uređenje prostora oko glavne tribine. Organizatori očekuju da će se time povećati broj sportskih i glazbenih događanja tijekom jeseni i zime.\n\nKlubovi koji koriste objekt podržali su zahvat, iako će dio treninga tijekom radova morati preseliti na pomoćne terene. Ako dinamika ostane prema planu, glavni dio radova trebao bi biti dovršen prije početka nove sezone.', 'sport', 'https://commons.wikimedia.org/wiki/Special:FilePath/Berliner_Olympiastadion_night.jpg', '2026-06-18 07:50:00', 4, 1),
('Izbornik testira novu postavu prije odlučujućeg kvalifikacijskog vikenda', 'Trenerski plan', 'Stručni stožer najavio je nekoliko promjena u rotaciji kako bi momčad dobila više brzine i čvrstine u obrani.', 'Na posljednjem treningu najviše se radilo na tranziciji i povratku u obranu nakon izgubljene lopte, jer je upravo taj segment u zadnjim utakmicama stvarao najviše problema. Izbornik je poručio da ne traži spektakl nego stabilnost, osobito protiv suparnika koji igraju agresivno na crti.\n\nNekoliko mlađih igrača moglo bi dobiti važniju ulogu već u prvoj utakmici, a stožer vjeruje da će širina rostera biti ključna u zgusnutom rasporedu. Atmosfera u reprezentaciji opisana je kao mirna i radna, bez velikih najava.', 'sport', 'https://commons.wikimedia.org/wiki/Special:FilePath/CZE_vs_FRA_%2801%29_-_2010_European_Men%27s_Handball_Championship.jpg', '2026-06-17 20:15:00', 3, 1);

-- Zadani admin korisnik stvara se automatski pri prvom otvaranju aplikacije.
-- Korisničko ime: admin
-- Lozinka: admin1234
