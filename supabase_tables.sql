-- Tabela de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    endereco TEXT NOT NULL,
    data_nascimento DATE NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de veículos
CREATE TABLE IF NOT EXISTS veiculos (
    id SERIAL PRIMARY KEY,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    ano INTEGER NOT NULL,
    placa VARCHAR(10) UNIQUE NOT NULL,
    cor VARCHAR(30) NOT NULL,
    chassi VARCHAR(17) UNIQUE NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255),
    destaque BOOLEAN DEFAULT FALSE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de vínculo entre cliente e veículo
CREATE TABLE IF NOT EXISTS cliente_veiculo (
    id SERIAL PRIMARY KEY,
    cliente_id INTEGER NOT NULL REFERENCES clientes(id) ON DELETE CASCADE,
    veiculo_id INTEGER NOT NULL REFERENCES veiculos(id) ON DELETE CASCADE,
    data_vinculo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(cliente_id, veiculo_id)
);

-- Tabela de contatos
CREATE TABLE IF NOT EXISTS contatos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    mensagem TEXT NOT NULL,
    veiculo_id INTEGER REFERENCES veiculos(id) ON DELETE SET NULL,
    lido BOOLEAN DEFAULT FALSE,
    data_contato TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir dados de exemplo para veículos
INSERT INTO veiculos (marca, modelo, ano, placa, cor, chassi, valor, descricao, destaque)
VALUES
    ('Toyota', 'Corolla', 2022, 'ABC1234', 'Prata', '9BRBL3HE6J0123456', 120000.00, 'Toyota Corolla XEi 2.0 Flex, completo, único dono, revisões em concessionária.', TRUE),
    ('Honda', 'Civic', 2021, 'DEF5678', 'Preto', '93HFC2630MZ123456', 115000.00, 'Honda Civic EXL 2.0, teto solar, bancos em couro, multimídia completa.', TRUE),
    ('Jeep', 'Compass', 2023, 'GHI9012', 'Branco', '98AAJ22PONE123456', 180000.00, 'Jeep Compass Limited 2.0 Diesel 4x4, pacote premium, teto panorâmico.', TRUE),
    ('Volkswagen', 'T-Cross', 2022, 'JKL3456', 'Vermelho', '9BWBJ6BF8M8123456', 130000.00, 'Volkswagen T-Cross Highline 1.4 TSI, completo, baixa quilometragem.', FALSE),
    ('Hyundai', 'HB20', 2021, 'MNO7890', 'Azul', '9BHBG51CBNP123456', 75000.00, 'Hyundai HB20 Diamond Plus 1.0 Turbo, completo, multimídia, câmera de ré.', FALSE),
    ('Chevrolet', 'Onix', 2022, 'PQR1234', 'Cinza', '9BGEA48A0NG123456', 78000.00, 'Chevrolet Onix Premier 1.0 Turbo, completo, MyLink, OnStar.', FALSE)
ON CONFLICT (placa) DO NOTHING;

