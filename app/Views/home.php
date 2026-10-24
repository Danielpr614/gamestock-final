<section class="hero" aria-label="Destaques GameStock">
<div class="hero-copy"><span class="eyebrow">ESCOLHA SEU PRÓXIMO UNIVERSO</span><h1>Seu próximo jogo.<br><span>Uma nova história.</span></h1><p>Dos mundos abertos às corridas decisivas.<br>Encontre seu próximo jogo e comece uma nova história.</p><a class="btn" href="#catalog">Explorar catálogo <span>↗</span></a><div class="hero-foot"><span>UM NOVO MUNDO A CADA JOGO</span><span>PC · PLAYSTATION · XBOX · NINTENDO</span></div></div>
<div class="hero-art featured" data-featured>
<?php foreach ($featured as $i=>$f): $heroImage=$f['titulo']==='Clair Obscur: Expedition 33'?'hero_expedition.jpg':($f['titulo']==='God of War Ragnarök'?'ps_ragnarok.jpg':cardImage($f['imagem'])); ?>
<div class="featured-slide" id="featured-<?= $i ?>" data-featured-slide <?= $i?'hidden':'' ?> aria-label="<?= e($f['titulo']) ?>">
<?php if ($heroImage): ?><img src="<?= e(asset('images/'.$heroImage)) ?>" alt="<?= e($f['titulo']) ?>" <?= $i?'loading="lazy"':'fetchpriority="high"' ?>><?php endif; ?>
<div class="featured-caption"><span class="eyebrow"><?= e($f['plataforma']) ?> · <?= e($f['categoria']) ?></span><h2><?= e($f['titulo']) ?></h2><div class="featured-bottom"><strong><?= money($f['preco']) ?></strong><a class="btn small" href="<?= e(url('game',['id'=>$f['id']])) ?>">Conhecer o jogo ↗</a></div></div>
</div>
<?php endforeach; ?>
<?php if (count($featured)>1): ?><div class="featured-controls" hidden aria-label="Escolher jogo em destaque"><?php foreach ($featured as $i=>$f): ?><button type="button" data-featured-target="<?= $i ?>" aria-controls="featured-<?= $i ?>" aria-pressed="<?= $i?'false':'true' ?>" aria-label="Ver destaque: <?= e($f['titulo']) ?>"><span><?= str_pad((string)($i+1),2,'0',STR_PAD_LEFT) ?></span></button><?php endforeach; ?><button type="button" class="featured-pause" data-featured-pause aria-label="Pausar destaques" title="Pausar destaques"><svg data-icon-pause width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 5v14M17 5v14" fill="none" stroke="currentColor" stroke-width="4"/></svg><svg data-icon-play hidden width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4l14 8-14 8z" fill="currentColor"/></svg></button></div><span class="sr-only" data-featured-status aria-live="polite" aria-atomic="true"></span><?php endif; ?>
<?php if (!$featured): ?><div class="featured-caption"><h2>Novas aventuras em breve.</h2><p>Explore os jogos disponíveis no catálogo.</p></div><?php endif; ?>
</div></section>
<section id="catalog"><div class="section-heading"><div><span class="eyebrow">ENCONTRE SEU PRÓXIMO FAVORITO</span><h2>Explore o catálogo<span class="accent">.</span></h2></div><span class="muted"><?= $total ?> <?= $total===1?'jogo encontrado':'jogos encontrados' ?></span></div>
<nav class="platforms" aria-label="Plataformas"><?php foreach ([''=>'Todos','PC'=>'PC','PlayStation'=>'PlayStation','Xbox'=>'Xbox','Nintendo'=>'Nintendo'] as $k=>$label): ?><a class="<?= $platform===$k?'selected':'' ?>" href="<?= e(url('home',['platform'=>$k,'q'=>$q,'category'=>$category,'sort'=>$sort])) ?>#catalog"><?= e($label) ?></a><?php endforeach; ?></nav>
<form class="filters" method="get" action="<?= e(url()) ?>#catalog"><input type="hidden" name="r" value="home"><input type="hidden" name="platform" value="<?= e($platform) ?>"><label>Buscar jogo<input name="q" maxlength="100" placeholder="Digite o nome de um jogo…" value="<?= e($q) ?>"></label><label>Categoria<select name="category"><option value="">Todas as categorias</option><?php foreach ($categories as $c): ?><option value="<?= e($c['id']) ?>" <?= (string)$category===(string)$c['id']?'selected':'' ?>><?= e($c['nome']) ?></option><?php endforeach; ?></select></label><label>Ordenar<select name="sort"><?php foreach (['title'=>'Nome: A a Z','price_asc'=>'Menor preço','price_desc'=>'Maior preço'] as $k=>$v): ?><option value="<?= e($k) ?>" <?= $sort===$k?'selected':'' ?>><?= e($v) ?></option><?php endforeach; ?></select></label><button class="btn">Buscar</button><a class="muted" href="<?= e(url('home',['platform'=>$platform])) ?>#catalog">Limpar</a></form>
<div class="game-grid"><?php foreach ($games as $g): require ROOT.'/app/Views/partials/card.php';endforeach; ?></div><?php if (!$games): ?><div class="empty panel"><h3>Nenhum jogo encontrado.</h3><p>Experimente outra busca ou limpe os filtros.</p><a class="btn quiet" href="<?= e(url()) ?>">Ver todos os jogos</a></div><?php endif; ?></section>

<?php if ($total): ?>
<div class="catalog-footer"><p class="muted">Exibindo <?= ($page-1)*$perPage+1 ?>–<?= min($page*$perPage,$total) ?> de <?= $total ?> jogos</p>
<nav class="pagination" aria-label="Páginas do catálogo">
<?php $pageParams=['platform'=>$platform,'q'=>$q,'category'=>$category,'sort'=>$sort]; ?>
<?php if ($page>1): ?><a class="page-arrow" rel="prev" href="<?= e(url('home',$pageParams+['page'=>$page-1])) ?>#catalog" aria-label="Página anterior">← Anterior</a><?php endif; ?>
<?php for ($n=max(1,$page-2);$n<=min($pages,$page+2);$n++): ?>
<a <?= $n===$page?'aria-current="page"':'' ?> href="<?= e(url('home',$pageParams+['page'=>$n])) ?>#catalog" aria-label="Página <?= $n ?>"><?= $n ?></a>
<?php endfor; ?>
<?php if ($page<$pages): ?><a class="page-arrow" rel="next" href="<?= e(url('home',$pageParams+['page'=>$page+1])) ?>#catalog" aria-label="Próxima página">Próxima →</a><?php endif; ?>
</nav></div>
<?php endif; ?>
