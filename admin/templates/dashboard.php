<?php
/**
 * Dashboard partial - BlackBOX Engines Manager
 *
 * Expected variables:
 *   $display_plugins       - array of all plugin data (merged & enriched)
 *   $compass_plugins       - array of Compass Engine plugin data
 *   $infrastructure_plugins - array of Infrastructure plugin data
 *   $total_count            - int
 *   $active_count           - int
 *   $active_value           - float
 *   $total_value            - float
 *   $total_standalone_value - float
 *   $tesseract_tiers        - array of tier definitions
 *   $obsidian_icon_url      - string URL to obsidian.png
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap blackbox-dashboard">
	<div class="blackbox-header">
		<img src="<?php echo esc_url( $obsidian_icon_url ); ?>" alt="BlackBOX">
		<div style="flex:1;">
			<div class="blackbox-header-titles">
				<h1>Software Manager</h1>
				<span class="blackbox-license-pill">
					<i class="fad fa-shield-check"></i>
					<span>Sovereign Protocol Active</span>
				</span>
			</div>
			<p style="margin:0;font-size:1.05rem;color:rgba(255,255,255,0.75);">
				Tesseract Tier Software Matrix &amp; Standalone Engine Licensing
			</p>
			<div class="blackbox-portfolio">
				<div class="blackbox-stat">
					<span class="blackbox-stat-value" id="stat-total-engines"><?php echo $total_count; ?></span>
					<span class="blackbox-stat-label">Engines</span>
				</div>
				<div class="blackbox-stat">
					<span class="blackbox-stat-value" id="stat-active-engines"><?php echo $active_count; ?></span>
					<span class="blackbox-stat-label">Active</span>
				</div>
				<div class="blackbox-stat">
					<span class="blackbox-stat-value gold">$<?php echo number_format( $active_value ); ?></span>
					<span class="blackbox-stat-label">Active Value / yr</span>
				</div>
				<div class="blackbox-stat">
					<span class="blackbox-stat-value cyan">$<?php echo number_format( $total_standalone_value ); ?></span>
					<span class="blackbox-stat-label">A La Carte Total / yr</span>
				</div>
				<div class="blackbox-stat">
					<span class="blackbox-stat-value">$<?php echo number_format( $total_value ); ?></span>
					<span class="blackbox-stat-label">Market Eqv / yr</span>
				</div>
			</div>
		</div>
	</div>

	<!-- Tesseract Tier & Software Filter Hub -->
	<div class="blackbox-filter-hub">
		<div class="blackbox-filter-header">
			<div class="blackbox-filter-title">
				<i class="fad fa-police-box" style="color:#62c9ff;"></i>
				<span>Tesseract Sovereignty Tiers</span>
			</div>
			<div class="blackbox-filter-counts">
				Showing <strong id="blackbox-visible-count"><?php echo $total_count; ?></strong> of <?php echo $total_count; ?> Engines
			</div>
		</div>

		<!-- Tier Pills Navigation Strip -->
		<div class="blackbox-tier-strip">
			<?php foreach ( $tesseract_tiers as $tier_key => $tier ) : 
				$is_all = $tier_key === 'all';
			?>
				<button 
					type="button" 
					class="blackbox-tier-btn <?php echo $is_all ? 'is-active' : ''; ?>"
					data-tier-filter="<?php echo esc_attr( $tier_key ); ?>"
					style="--tier-accent: <?php echo esc_attr( $tier['color'] ); ?>;"
				>
					<span class="tier-indicator" style="background: <?php echo esc_attr( $tier['color'] ); ?>;"></span>
					<i class="<?php echo esc_attr( $tier['icon'] ); ?>" style="font-size:0.75rem;"></i>
					<span class="tier-name"><?php echo esc_html( $tier['name'] ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<!-- Secondary Search & Component Type Controls -->
		<div class="blackbox-subfilter-bar">
			<div class="blackbox-search-wrap">
				<i class="fal fa-search blackbox-search-icon"></i>
				<input 
					type="text" 
					id="blackbox-search" 
					class="blackbox-search-input" 
					placeholder="Search engines, SaaS replacements, or keywords..."
					autocomplete="off"
				>
				<button type="button" id="blackbox-search-clear" class="blackbox-search-clear" style="display:none;">
					<i class="fal fa-times"></i>
				</button>
			</div>

			<div class="blackbox-subfilter-pills">
				<div class="blackbox-pill-group" data-filter-group="status">
					<button type="button" class="blackbox-subfilter-btn is-active" data-status-filter="all">All Status</button>
					<button type="button" class="blackbox-subfilter-btn" data-status-filter="active">Active</button>
					<button type="button" class="blackbox-subfilter-btn" data-status-filter="inactive">Inactive</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Engines & Software Grid -->
	<div class="blackbox-grid" id="blackbox-plugins-grid">
		<?php foreach ( $display_plugins as $plugin ) :
			$status_class = $plugin['active'] ? 'badge-active' : 'badge-inactive';
			$status_text = $plugin['active'] ? 'Active' : 'Inactive';
			$type_class = $plugin['type'] === 'Compass Engine' ? 'is-compass' : 'is-infrastructure';
			$search_string = strtolower( $plugin['name'] . ' ' . $plugin['desc'] . ' ' . $plugin['replaces'] . ' ' . $plugin['tier_name'] . ' ' . $plugin['comp_type'] );
		?>
			<div 
				class="blackbox-card <?php echo $plugin['active'] ? 'is-active' : ''; ?> <?php echo $type_class; ?>"
				data-tier="<?php echo esc_attr( $plugin['min_tier'] ); ?>"
				data-status="<?php echo $plugin['active'] ? 'active' : 'inactive'; ?>"
				data-search="<?php echo esc_attr( $search_string ); ?>"
				data-standalone="<?php echo esc_attr( $plugin['standalone_price'] ); ?>"
				style="--card-tier-color: <?php echo esc_attr( $plugin['tier_color'] ); ?>;"
			>
				<!-- Top-Right Tesseract Tier Ribbon -->
				<div class="blackbox-tier-ribbon" title="<?php echo esc_attr( $plugin['tier_name'] ); ?> Tier">
					<i class="<?php echo esc_attr( $plugin['tier_icon'] ); ?>"></i>
					<span class="tier-ribbon-name"><?php echo esc_html( $plugin['tier_name'] ); ?></span>
				</div>

				<div class="blackbox-card-bg-wrap">
					<img 
						src="<?php echo esc_url( $plugin['icon'], ['http', 'https', 'data'] ); ?>" 
						class="blackbox-card-bg-icon" 
						alt="" 
						data-fallback="<?php echo esc_attr( $plugin['fallback_icon'] ); ?>" 
						onerror="if(this.dataset.fallback &amp;&amp; this.src !== this.dataset.fallback){this.src=this.dataset.fallback;}else{this.style.display='none';}"
					>
				</div>
				
				<div class="blackbox-card-header">
					<img 
						src="<?php echo esc_url( $plugin['icon'], ['http', 'https', 'data'] ); ?>" 
						class="blackbox-card-icon" 
						alt="Icon" 
						data-fallback="<?php echo esc_attr( $plugin['fallback_icon'] ); ?>" 
						onerror="if(this.dataset.fallback &amp;&amp; this.src !== this.dataset.fallback){this.src=this.dataset.fallback;}"
					>
					<div class="blackbox-card-title-area">
						<h2><?php echo esc_html( $plugin['name'] ); ?></h2>
						<div class="blackbox-card-meta">
							<span>v<?php echo esc_html( $plugin['version'] ); ?></span>
							<span>&bull;</span>
							<span><?php echo esc_html( $plugin['type'] ); ?></span>
						</div>
					</div>
				</div>

				<div class="blackbox-card-desc">
					<?php echo wp_kses_post( $plugin['desc'] ); ?>
				</div>

				<div class="blackbox-card-pricing-bar">
					<div class="blackbox-standalone-tag">
						<i class="fal fa-tag"></i>
						<span><strong>$<?php echo esc_html( $plugin['standalone_price'] ); ?></strong> / yr standalone</span>
					</div>
					<span class="blackbox-included-pill" title="Included with <?php echo esc_attr( $plugin['tier_name'] ); ?> or higher license">
						<i class="fal fa-check-circle" style="color:<?php echo esc_attr( $plugin['tier_color'] ); ?>;"></i>
						<span>Included in <?php echo esc_html( $plugin['tier_name'] ); ?>+</span>
					</span>
				</div>

				<div class="blackbox-card-footer">
					<span class="blackbox-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
					<div class="blackbox-actions">
						<?php if ( $plugin['active'] ) : ?>
							<a href="<?php echo esc_url( $plugin['go_url'] ); ?>" class="btn-go">
								<span>Go</span>
								<i class="fad fa-traffic-light-go"></i>
							</a>
							<button type="button" class="btn-toggle btn-on" data-action="deactivate" data-plugin="<?php echo esc_attr( $plugin['path'] ); ?>" data-go="<?php echo esc_attr( $plugin['go_url'] ); ?>">
								<span>On</span>
								<i class="fad fa-toggle-on"></i>
							</button>
						<?php else : ?>
							<a href="#" class="btn-go is-disabled" tabindex="-1" aria-disabled="true">
								<span>Go</span>
								<i class="fad fa-traffic-light-stop"></i>
							</a>
							<button type="button" class="btn-toggle btn-off" data-action="activate" data-plugin="<?php echo esc_attr( $plugin['path'] ); ?>" data-go="<?php echo esc_attr( $plugin['go_url'] ); ?>">
								<span>Off</span>
								<i class="fad fa-toggle-off"></i>
							</button>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( ! empty( $plugin['value'] ) ) : ?>
				<div class="blackbox-card-ribbon">
					<span class="blackbox-card-ribbon-value">&asymp; $<?php echo number_format( $plugin['value'] ); ?>/yr</span>
					<?php if ( ! empty( $plugin['replaces'] ) ) : ?>
						<span class="blackbox-card-ribbon-label"> &middot; Market Eqv: <?php echo esc_html( $plugin['replaces'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $plugin['rationale'] ) ) : ?>
						<div class="blackbox-ribbon-tooltip">
							<?php echo esc_html( $plugin['rationale'] ); ?>
						</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

	<!-- Empty Filter State -->
	<div id="blackbox-empty-state" class="blackbox-empty-state" style="display:none;">
		<div class="blackbox-empty-inner">
			<i class="fad fa-box-open blackbox-empty-icon"></i>
			<h3>No Engines Match Filter</h3>
			<p>Try selecting a different Tesseract tier, clearing your search query, or changing status filters.</p>
			<button type="button" id="blackbox-reset-filters" class="blackbox-reset-btn">
				<i class="fal fa-undo"></i> Reset All Filters
			</button>
		</div>
	</div>
</div>
