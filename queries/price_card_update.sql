-- North America
UPDATE price_cards SET market = 'North America' WHERE country IN ('Canada', 'United States');

-- Rest of Africa
UPDATE price_cards SET market = 'Rest of Africa' WHERE country IN (
    'Algeria', 'Angola', 'Benin', 'Botswana', 'Burkina Faso', 'Burundi', 'Cameroon', 
    'Chad', 'Republic of the Congo (Brazzaville)', 'Eritrea', 'Ethiopia', 'Gabon', 'Gambia', 
    'Ghana', 'Guinea-Bissau', 'Ivory Coast', 'Kenya', 'Lesotho', 'Liberia', 'Libya', 
    'Madagascar', 'Malawi', 'Mali', 'Mauritania', 'Morocco', 'Mozambique', 'Namibia', 
    'Niger', 'Rwanda', 'Senegal', 'Sierra Leone', 'Somalia', 'South Sudan', 'Sudan', 
    'Swaziland', 'Tanzania', 'Togo', 'Tunisia', 'Uganda', 'Zambia'
);

-- Rest of Asia Pacific
UPDATE price_cards SET market = 'Rest of Asia Pacific' WHERE country IN (
    'Afghanistan', 'Australia', 'Bangladesh', 'Cambodia', 'China', 'Hong Kong', 'Japan', 
    'Laos', 'Mongolia', 'Nepal', 'New Zealand', 'Papua New Guinea', 'Philippines', 
    'Singapore', 'Sri Lanka', 'Taiwan', 'Tajikistan', 'Thailand', 'Turkmenistan', 
    'Uzbekistan', 'Vietnam'
);

-- Rest of Central & Eastern Europe
UPDATE price_cards SET market = 'Rest of Central & Eastern Europe' WHERE country IN (
    'Albania', 'Armenia', 'Azerbaijan', 'Belarus', 'Bulgaria', 'Croatia', 'Czech Republic', 
    'Georgia', 'Greece', 'Hungary', 'Latvia', 'Lithuania', 'Moldova', 'North Macedonia', 
    'Poland', 'Romania', 'Serbia', 'Slovakia', 'Slovenia', 'Ukraine'
);

-- Rest of Latin America
UPDATE price_cards SET market = 'Rest of Latin America' WHERE country IN (
    'Bolivia', 'Costa Rica', 'Dominican Republic', 'Ecuador', 'El Salvador', 
    'Guatemala', 'Haiti', 'Honduras', 'Jamaica', 'Nicaragua', 'Panama', 
    'Paraguay', 'Puerto Rico', 'Uruguay', 'Venezuela'
);

-- Rest of Middle East
UPDATE price_cards SET market = 'Rest of Middle East' WHERE country IN (
    'Bahrain', 'Iraq', 'Jordan', 'Kuwait', 'Lebanon', 'Oman', 'Qatar', 'Yemen'
);

-- Rest of Western Europe
UPDATE price_cards SET market = 'Rest of Western Europe' WHERE country IN (
    'Austria', 'Belgium', 'Denmark', 'Finland', 'Ireland', 'Norway', 'Portugal', 
    'Sweden', 'Switzerland'
);

-- Other
UPDATE price_cards SET market = 'Other' WHERE country NOT IN (
    'Canada', 'United States', 'Algeria', 'Angola', 'Benin', 'Botswana', 'Burkina Faso', 
    'Burundi', 'Cameroon', 'Chad', 'Republic of the Congo (Brazzaville)', 'Eritrea', 'Ethiopia', 
    'Gabon', 'Gambia', 'Ghana', 'Guinea-Bissau', 'Ivory Coast', 'Kenya', 'Lesotho', 'Liberia', 
    'Libya', 'Madagascar', 'Malawi', 'Mali', 'Mauritania', 'Morocco', 'Mozambique', 'Namibia', 
    'Niger', 'Rwanda', 'Senegal', 'Sierra Leone', 'Somalia', 'South Sudan', 'Sudan', 
    'Swaziland', 'Tanzania', 'Togo', 'Tunisia', 'Uganda', 'Zambia', 'Afghanistan', 
    'Australia', 'Bangladesh', 'Cambodia', 'China', 'Hong Kong', 'Japan', 'Laos', 
    'Mongolia', 'Nepal', 'New Zealand', 'Papua New Guinea', 'Philippines', 'Singapore', 
    'Sri Lanka', 'Taiwan', 'Tajikistan', 'Thailand', 'Turkmenistan', 'Uzbekistan', 'Vietnam', 
    'Albania', 'Armenia', 'Azerbaijan', 'Belarus', 'Bulgaria', 'Croatia', 'Czech Republic', 
    'Georgia', 'Greece', 'Hungary', 'Latvia', 'Lithuania', 'Moldova', 'North Macedonia', 
    'Poland', 'Romania', 'Serbia', 'Slovakia', 'Slovenia', 'Ukraine', 'Bolivia', 
    'Costa Rica', 'Dominican Republic', 'Ecuador', 'El Salvador', 'Guatemala', 'Haiti', 
    'Honduras', 'Jamaica', 'Nicaragua', 'Panama', 'Paraguay', 'Puerto Rico', 'Uruguay', 
    'Venezuela', 'Bahrain', 'Iraq', 'Jordan', 'Kuwait', 'Lebanon', 'Oman', 'Qatar', 
    'Yemen', 'Austria', 'Belgium', 'Denmark', 'Finland', 'Ireland', 'Norway', 'Portugal', 
    'Sweden', 'Switzerland'
) and updated=false;

