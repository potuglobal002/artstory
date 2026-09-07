import * as THREE from 'three';
import { VRButton } from 'three/addons/webxr/VRButton.js';

const root = document.querySelector('[data-virtual-gallery]');

if (root) {
    const sceneData = JSON.parse(document.getElementById('virtual-gallery-data').textContent);
    const canvasHost = root.querySelector('[data-virtual-gallery-canvas]');
    const artworkDrawer = root.querySelector('[data-gallery-artwork-drawer]');
    const artworkTitle = root.querySelector('[data-gallery-artwork-title]');
    const artworkImage = root.querySelector('[data-gallery-artwork-image]');
    const artworkCode = root.querySelector('[data-gallery-artwork-code]');
    const artworkArtist = root.querySelector('[data-gallery-artwork-artist]');
    const artworkMedium = root.querySelector('[data-gallery-artwork-medium]');
    const artworkDimensions = root.querySelector('[data-gallery-artwork-dimensions]');
    const artworkYear = root.querySelector('[data-gallery-artwork-year]');
    const artworkLink = root.querySelector('[data-gallery-artwork-link]');
    const roomButtons = [...root.querySelectorAll('[data-gallery-room]')];
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(62, 1, 0.1, 100);
    const raycaster = new THREE.Raycaster();
    const pointer = new THREE.Vector2();
    const textureLoader = new THREE.TextureLoader();
    let roomGroup = new THREE.Group();
    let pointerStart = null;
    let lastPointer = null;
    let isLooking = false;
    let currentRoom = 0;
    let yaw = 0;
    let pitch = -0.06;
    let panoramaTexture = null;
    let panoramaSphere = null;

    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(canvasHost.clientWidth, canvasHost.clientHeight);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.12;
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.xr.enabled = true;
    canvasHost.appendChild(renderer.domElement);

    scene.background = new THREE.Color(0xf7f5ef);
    scene.add(new THREE.HemisphereLight(0xffffff, 0xf1eee8, 2.1));
    const keyLight = new THREE.DirectionalLight(0xfff4dd, 2.8);
    keyLight.position.set(2, 6, 3);
    scene.add(keyLight);
    const fillLight = new THREE.PointLight(0xffffff, 1.6, 40);
    fillLight.position.set(-3, 3.5, -2);
    scene.add(fillLight);
    scene.add(roomGroup);

    const disposeGroup = (group) => {
        group.traverse((object) => {
            object.geometry?.dispose?.();
            if (Array.isArray(object.material)) object.material.forEach((material) => material.dispose?.());
            else object.material?.dispose?.();
        });
    };

    const updateCameraLook = () => {
        const direction = new THREE.Vector3(0, 0, -1).applyEuler(new THREE.Euler(pitch, yaw, 0, 'YXZ'));
        camera.lookAt(camera.position.clone().add(direction));
    };

    const moveCamera = (direction, distance = 0.72) => {
        const room = sceneData.rooms[currentRoom];
        const forward = new THREE.Vector3(-Math.sin(yaw), 0, -Math.cos(yaw));
        const right = new THREE.Vector3(Math.cos(yaw), 0, -Math.sin(yaw));
        const delta = direction === 'forward' ? forward : direction === 'back' ? forward.negate() : direction === 'left' ? right.negate() : right;
        const nextX = THREE.MathUtils.clamp(camera.position.x + delta.x * distance, -room.width / 2 + 1.1, room.width / 2 - 1.1);
        const nextZ = THREE.MathUtils.clamp(camera.position.z + delta.z * distance, -room.depth / 2 + 1.1, room.depth / 2 - 1.1);
        camera.position.set(nextX, camera.position.y, nextZ);
        updateCameraLook();
    };

    const roomPosition = (item, room) => {
        const width = room.width;
        const depth = room.depth;
        const inward = 0.07;
        const rotation = THREE.MathUtils.degToRad(item.rotationY || 0);

        if (item.wall === 'left') return { position: [-width / 2 + inward, item.offsetY, -item.offsetX], rotation: Math.PI / 2 + rotation };
        if (item.wall === 'right') return { position: [width / 2 - inward, item.offsetY, item.offsetX], rotation: -Math.PI / 2 + rotation };
        if (item.wall === 'front') return { position: [item.offsetX, item.offsetY, depth / 2 - inward], rotation: Math.PI + rotation };
        if (item.wall === 'divider-left') return { position: [-width * 0.18 + 0.12, item.offsetY, item.offsetX], rotation: Math.PI / 2 + rotation };
        if (item.wall === 'divider-right') return { position: [width * 0.2 - 0.12, item.offsetY, item.offsetX], rotation: -Math.PI / 2 + rotation };

        return { position: [item.offsetX, item.offsetY, -depth / 2 + inward], rotation };
    };

    const addArtwork = (item, room) => {
        const group = new THREE.Group();
        const { position, rotation } = roomPosition(item, room);
        group.position.set(...position);
        group.rotation.y = rotation;
        group.userData = { artwork: item };

        const image = new THREE.Mesh(
            new THREE.PlaneGeometry(1, 1),
            new THREE.MeshBasicMaterial({ color: 0xddd6c9, side: THREE.FrontSide }),
        );
        const frame = new THREE.Group();
        frame.userData = { artwork: item };
        const frameMaterial = new THREE.MeshStandardMaterial({ color: item.frameColor || '#1a1612', roughness: 0.42, metalness: 0.08 });
        const topRail = new THREE.Mesh(new THREE.BoxGeometry(1, 1, 0.09), frameMaterial);
        const bottomRail = topRail.clone();
        const leftRail = new THREE.Mesh(new THREE.BoxGeometry(1, 1, 0.09), frameMaterial);
        const rightRail = leftRail.clone();
        frame.add(topRail, bottomRail, leftRail, rightRail);
        const plaque = new THREE.Mesh(
            new THREE.BoxGeometry(0.42, 0.055, 0.025),
            new THREE.MeshStandardMaterial({ color: 0xb89d68, roughness: 0.55 }),
        );
        const setDimensions = (ratio = 1) => {
            const height = item.height || Math.max(0.4, item.width / ratio);
            image.scale.set(item.width, height, 1);
            const railThickness = Math.min(0.11, Math.max(0.055, Math.min(item.width, height) * 0.045));
            topRail.scale.set(item.width + railThickness * 2, railThickness, 1);
            bottomRail.scale.set(item.width + railThickness * 2, railThickness, 1);
            leftRail.scale.set(railThickness, height, 1);
            rightRail.scale.set(railThickness, height, 1);
            topRail.position.set(0, (height + railThickness) / 2, 0);
            bottomRail.position.set(0, -(height + railThickness) / 2, 0);
            leftRail.position.set(-(item.width + railThickness) / 2, 0, 0);
            rightRail.position.set((item.width + railThickness) / 2, 0, 0);
            plaque.position.set(0, -(height / 2) - 0.1, 0.07);
        };

        setDimensions();
        image.position.z = 0.055;
        frame.traverse((part) => {
            part.castShadow = true;
        });
        group.add(frame, image, plaque);
        roomGroup.add(group);

        if (item.imageUrl) {
            textureLoader.load(item.imageUrl, (texture) => {
                texture.colorSpace = THREE.SRGBColorSpace;
                image.material.map = texture;
                image.material.needsUpdate = true;
                const ratio = texture.image.width / texture.image.height;
                setDimensions(ratio || 1);
            });
        }
    };

    const applyPanorama = (url) => {
        if (panoramaSphere) {
            scene.remove(panoramaSphere);
            panoramaSphere.geometry.dispose();
            panoramaSphere.material.dispose();
            panoramaSphere = null;
        }
        if (panoramaTexture) {
            panoramaTexture.dispose();
            panoramaTexture = null;
        }
        scene.background = new THREE.Color(0xf7f5ef);
        scene.environment = null;
        if (!url) return;

        textureLoader.load(url, (texture) => {
            texture.colorSpace = THREE.SRGBColorSpace;
            panoramaTexture = texture;
            panoramaSphere = new THREE.Mesh(
                new THREE.SphereGeometry(20, 64, 40),
                new THREE.MeshBasicMaterial({ map: texture, side: THREE.BackSide, depthWrite: false }),
            );
            panoramaSphere.renderOrder = -1;
            scene.add(panoramaSphere);
        });
    };

    const addGalleryLighting = (room) => {
        const trackMaterial = new THREE.MeshStandardMaterial({ color: 0x24211e, roughness: 0.48, metalness: 0.35 });
        const positions = [
            [-room.width * 0.3, room.height - 0.28, -room.depth * 0.23, 0, 2.8, -room.depth / 2 + 0.35],
            [0, room.height - 0.28, -room.depth * 0.23, 0, 2.8, -room.depth / 2 + 0.35],
            [room.width * 0.3, room.height - 0.28, -room.depth * 0.23, 0, 2.8, -room.depth / 2 + 0.35],
            [-room.width * 0.38, room.height - 0.28, room.depth * 0.12, -room.width / 2 + 0.35, 2.8, 0],
            [room.width * 0.38, room.height - 0.28, room.depth * 0.12, room.width / 2 - 0.35, 2.8, 0],
        ];

        positions.forEach(([x, y, z, targetX, targetY, targetZ]) => {
            const fixture = new THREE.Mesh(new THREE.CylinderGeometry(0.09, 0.13, 0.3, 12), trackMaterial);
            fixture.position.set(x, y - 0.18, z);
            roomGroup.add(fixture);

            const spotlight = new THREE.SpotLight(0xfff0d0, 28, 11, Math.PI / 6, 0.58, 1.1);
            spotlight.position.set(x, y - 0.35, z);
            spotlight.target.position.set(targetX, targetY, targetZ);
            roomGroup.add(spotlight, spotlight.target);
        });
    };

    const addBaseboards = (room) => {
        const material = new THREE.MeshStandardMaterial({ color: 0xd9d1c5, roughness: 0.7 });
        const depthBoard = new THREE.BoxGeometry(room.width, 0.14, 0.12);
        const widthBoard = new THREE.BoxGeometry(0.12, 0.14, room.depth);
        [[depthBoard, 0, 0.07, -room.depth / 2], [depthBoard, 0, 0.07, room.depth / 2], [widthBoard, -room.width / 2, 0.07, 0], [widthBoard, room.width / 2, 0.07, 0]]
            .forEach(([geometry, x, y, z]) => {
                const baseboard = new THREE.Mesh(geometry, material);
                baseboard.position.set(x, y, z);
                roomGroup.add(baseboard);
            });
    };

    const addGalleryArchitecture = (room) => {
        const partitionMaterial = new THREE.MeshStandardMaterial({ color: room.wallColor, roughness: 0.92 });
        if (room.showFloorGrid) {
            const tileGrid = new THREE.GridHelper(Math.max(room.width, room.depth), Math.max(room.width, room.depth), 0xaeb1b4, 0xc9cbcd);
            tileGrid.scale.z = room.depth / room.width;
            tileGrid.position.y = 0.012;
            roomGroup.add(tileGrid);
        }

        const addPartition = (x, z, length) => {
            const partition = new THREE.Mesh(new THREE.BoxGeometry(0.2, room.height, length), partitionMaterial);
            partition.position.set(x, room.height / 2, z);
            partition.castShadow = true;
            partition.receiveShadow = true;
            roomGroup.add(partition);
        };

        // These staggered dividers create connected exhibition bays and a clear walking route.
        if (room.showPartitions) {
            addPartition(-room.width * 0.18, -room.depth * 0.02, room.depth * 0.42);
            addPartition(room.width * 0.2, -room.depth * 0.02, room.depth * 0.34);
        }
    };

    const renderRoom = (index) => {
        currentRoom = index;
        const room = sceneData.rooms[index];
        disposeGroup(roomGroup);
        scene.remove(roomGroup);
        roomGroup = new THREE.Group();
        scene.add(roomGroup);

        if (!room.environmentUrl) {
            const wallMaterial = new THREE.MeshStandardMaterial({ color: room.wallColor, roughness: 0.92, side: THREE.DoubleSide });
            const floorMaterial = new THREE.MeshStandardMaterial({ color: room.floorColor, roughness: 0.48, metalness: 0.04 });
            const ceilingMaterial = new THREE.MeshStandardMaterial({ color: room.ceilingColor, roughness: 0.94, side: THREE.DoubleSide });
            const floor = new THREE.Mesh(new THREE.PlaneGeometry(room.width, room.depth), floorMaterial);
            floor.rotation.x = -Math.PI / 2;
            floor.receiveShadow = true;
            const ceiling = new THREE.Mesh(new THREE.PlaneGeometry(room.width, room.depth), ceilingMaterial);
            ceiling.rotation.x = Math.PI / 2;
            ceiling.position.y = room.height;
            const back = new THREE.Mesh(new THREE.PlaneGeometry(room.width, room.height), wallMaterial);
            back.position.set(0, room.height / 2, -room.depth / 2);
            const front = new THREE.Mesh(new THREE.PlaneGeometry(room.width, room.height), wallMaterial);
            front.rotation.y = Math.PI;
            front.position.set(0, room.height / 2, room.depth / 2);
            const left = new THREE.Mesh(new THREE.PlaneGeometry(room.depth, room.height), wallMaterial);
            left.rotation.y = Math.PI / 2;
            left.position.set(-room.width / 2, room.height / 2, 0);
            const right = new THREE.Mesh(new THREE.PlaneGeometry(room.depth, room.height), wallMaterial);
            right.rotation.y = -Math.PI / 2;
            right.position.set(room.width / 2, room.height / 2, 0);
            roomGroup.add(floor, ceiling, back, front, left, right);
            addBaseboards(room);
            addGalleryArchitecture(room);
            addGalleryLighting(room);
        }

        room.artworks.forEach((item) => addArtwork(item, room));
        camera.position.set(...room.camera);
        yaw = 0;
        pitch = -0.06;
        updateCameraLook();
        applyPanorama(room.environmentUrl || room.panoramaUrl);
        closeArtworkDrawer();
        roomButtons.forEach((button, buttonIndex) => button.classList.toggle('is-active', buttonIndex === index));
    };

    const inspectArtwork = (artwork) => {
        artworkTitle.textContent = artwork.title;
        artworkCode.textContent = artwork.code || 'Artwork';
        artworkArtist.textContent = artwork.artist;
        artworkMedium.textContent = artwork.medium || 'Not specified';
        artworkDimensions.textContent = artwork.dimensions || 'Not specified';
        artworkYear.textContent = artwork.year || 'Not specified';
        artworkImage.src = artwork.imageUrl || '';
        artworkImage.alt = artwork.title;
        artworkImage.hidden = !artwork.imageUrl;
        artworkLink.href = artwork.url;
        artworkLink.hidden = false;
        artworkDrawer.classList.add('is-open');
        artworkDrawer.setAttribute('aria-hidden', 'false');
    };

    const closeArtworkDrawer = () => {
        artworkDrawer.classList.remove('is-open');
        artworkDrawer.setAttribute('aria-hidden', 'true');
    };

    const selectArtworkAt = (event) => {
        const bounds = renderer.domElement.getBoundingClientRect();
        pointer.set(((event.clientX - bounds.left) / bounds.width) * 2 - 1, -((event.clientY - bounds.top) / bounds.height) * 2 + 1);
        raycaster.setFromCamera(pointer, camera);
        const hit = raycaster.intersectObjects(roomGroup.children, true)
            .find((intersection) => intersection.object.parent?.userData?.artwork || intersection.object.userData?.artwork);
        const artwork = hit?.object.parent?.userData?.artwork || hit?.object.userData?.artwork;

        if (artwork) inspectArtwork(artwork);
    };

    renderer.domElement.addEventListener('pointerdown', (event) => {
        pointerStart = [event.clientX, event.clientY];
        lastPointer = [event.clientX, event.clientY];
        isLooking = true;
        renderer.domElement.setPointerCapture?.(event.pointerId);
    });

    renderer.domElement.addEventListener('pointerup', (event) => {
        isLooking = false;
        renderer.domElement.releasePointerCapture?.(event.pointerId);
        if (!pointerStart || Math.hypot(event.clientX - pointerStart[0], event.clientY - pointerStart[1]) > 7) return;
        selectArtworkAt(event);
    });

    renderer.domElement.addEventListener('click', selectArtworkAt);

    renderer.domElement.addEventListener('wheel', (event) => {
        event.preventDefault();

        const amount = THREE.MathUtils.clamp(Math.abs(event.deltaY || event.deltaX) * 0.009, 0.2, 0.82);
        if (event.shiftKey) {
            moveCamera(event.deltaY > 0 ? 'right' : 'left', amount);
            return;
        }

        moveCamera(event.deltaY > 0 ? 'forward' : 'back', amount);
    }, { passive: false });

    renderer.domElement.addEventListener('pointermove', (event) => {
        if (isLooking && lastPointer) {
            yaw -= (event.clientX - lastPointer[0]) * 0.005;
            pitch = THREE.MathUtils.clamp(pitch - (event.clientY - lastPointer[1]) * 0.004, -1.28, 1.28);
            updateCameraLook();
            lastPointer = [event.clientX, event.clientY];
            return;
        }

        const bounds = renderer.domElement.getBoundingClientRect();
        pointer.set(((event.clientX - bounds.left) / bounds.width) * 2 - 1, -((event.clientY - bounds.top) / bounds.height) * 2 + 1);
        raycaster.setFromCamera(pointer, camera);
        const isArtwork = raycaster.intersectObjects(roomGroup.children, true).some((intersection) => intersection.object.parent?.userData?.artwork || intersection.object.userData?.artwork);
        renderer.domElement.style.cursor = isArtwork ? 'pointer' : 'grab';
    });

    roomButtons.forEach((button) => button.addEventListener('click', () => renderRoom(Number(button.dataset.galleryRoom))));
    root.querySelectorAll('[data-gallery-move]').forEach((button) => button.addEventListener('click', () => moveCamera(button.dataset.galleryMove)));
    root.querySelector('[data-gallery-artwork-close]').addEventListener('click', closeArtworkDrawer);
    root.querySelector('[data-gallery-fullscreen]').addEventListener('click', () => {
        if (!document.fullscreenElement) root.requestFullscreen?.();
        else document.exitFullscreen?.();
    });

    if (navigator.xr?.isSessionSupported) {
        navigator.xr.isSessionSupported('immersive-vr').then((supported) => {
            if (supported) root.querySelector('[data-gallery-vr]').appendChild(VRButton.createButton(renderer));
        });
    }

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeArtworkDrawer();
            return;
        }
        const direction = { ArrowUp: 'forward', w: 'forward', W: 'forward', ArrowDown: 'back', s: 'back', S: 'back', ArrowLeft: 'left', a: 'left', A: 'left', ArrowRight: 'right', d: 'right', D: 'right' }[event.key];
        if (!direction || event.metaKey || event.ctrlKey || event.altKey) return;
        event.preventDefault();
        moveCamera(direction);
    });

    const resize = () => {
        const { clientWidth, clientHeight } = canvasHost;
        camera.aspect = clientWidth / clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(clientWidth, clientHeight);
    };

    window.addEventListener('resize', resize);
    renderRoom(0);
    renderer.setAnimationLoop(() => {
        renderer.render(scene, camera);
    });
}
